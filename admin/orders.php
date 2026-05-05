<?php
$base_path = '../';
$page_title='Orders'; $active_nav='orders';
require '../layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_action'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['order_action'];
    $status_map = [
        'process' => 'completed',
        'deliver' => 'delivered',
        'cancel' => 'cancelled',
    ];

    if (isset($status_map[$action])) {
        $new_status = $status_map[$action];
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        header("Location: /inventory_project/admin/orders.php?updated=1");
        exit;
    }
}

$orders = $conn->query("SELECT o.*, GROUP_CONCAT(oi.product_name, ' ×', oi.quantity SEPARATOR ', ') AS items FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id GROUP BY o.id ORDER BY o.created_at DESC");

function sb($s){$m=['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];return"<span class='badge ".($m[$s]??'b-gray')."'>".ucfirst($s)."</span>";}
function step($status){$steps=['pending'=>1,'completed'=>2,'delivered'=>3,'cancelled'=>0];return$steps[$status]??0;}
?>

<div style="margin-bottom:20px"><h2 style="font-size:20px;font-weight:800">📦 All Orders</h2></div>

<?php if(isset($_GET['updated'])):?>
<div class="alert alert-success" style="font-size:15px">
  ✅ Order status updated successfully.
</div>
<?php endif;?>

<?php if($orders->num_rows>0):?>
<div style="display:flex;flex-direction:column;gap:16px">
<?php while($o=$orders->fetch_assoc()):
  $s=step($o['status']);
?>
<div class="card" style="margin-bottom:0">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
      <span class="td-id" style="font-size:14px;font-weight:700;color:var(--accent)"><?=htmlspecialchars($o['order_no'])?></span>
      <span style="margin-left:12px"><?=sb($o['status'])?></span>
      <span style="margin-left:12px;font-size:13px;color:var(--text2)">👤 <?=htmlspecialchars($o['customer_name'])?></span>
    </div>
    <div style="font-size:12px;color:var(--text3)"><?=date('d M Y, h:i A',strtotime($o['created_at']))?></div>
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:1fr auto;gap:16px;align-items:start">
      <div>
        <div style="font-size:13px;color:var(--text2);margin-bottom:6px">Items ordered:</div>
        <div style="font-size:14px;font-weight:500"><?=htmlspecialchars($o['items']??'—')?></div>
        <?php if($o['address']):?>
        <div style="font-size:12px;color:var(--text3);margin-top:8px">📍 <?=htmlspecialchars($o['address'])?></div>
        <?php endif;?>
        <?php if($o['customer_phone']):?>
        <div style="font-size:12px;color:var(--text3)">📞 <?=htmlspecialchars($o['customer_phone'])?></div>
        <?php endif;?>
        <?php if($o['payment_method']):?>
        <div style="font-size:12px;color:var(--text3);margin-top:4px">
          <?=$o['payment_method']==='cash'?'💵 Cash on Delivery':'🏦 Bank Transfer'?>
          <?php if($o['transaction_id']):?> — <?=htmlspecialchars($o['transaction_id'])?><?php endif;?>
        </div>
        <?php endif;?>
      </div>
      <div style="text-align:right">
        <div style="font-size:22px;font-weight:800;color:var(--accent)">৳<?=number_format((float)$o['total_price'],2)?></div>
        <div style="font-size:11px;color:var(--text3)">Total amount</div>
      </div>
    </div>

    <?php if($o['status'] !== 'cancelled'): ?>
    <div style="margin-top:16px;display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end">
      <?php if($o['status']==='pending'): ?>
      <form method="post" style="display:inline">
        <input type="hidden" name="order_id" value="<?=htmlspecialchars($o['id'])?>">
        <button type="submit" name="order_action" value="process" class="btn btn-sm btn-primary">Start Processing</button>
      </form>
      <form method="post" style="display:inline">
        <input type="hidden" name="order_id" value="<?=htmlspecialchars($o['id'])?>">
        <button type="submit" name="order_action" value="cancel" class="btn btn-sm btn-outline">Cancel Order</button>
      </form>
      <?php elseif($o['status']==='completed'): ?>
      <form method="post" style="display:inline">
        <input type="hidden" name="order_id" value="<?=htmlspecialchars($o['id'])?>">
        <button type="submit" name="order_action" value="deliver" class="btn btn-sm btn-primary">Mark Delivered</button>
      </form>
      <?php elseif($o['status']==='delivered'): ?>
      <span class="badge b-green">Delivered</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if($o['status']!=='cancelled'):?>
    <!-- Progress tracker -->
    <div style="margin-top:18px">
      <div style="display:flex;align-items:center;gap:0">
        <?php
        $steps=[['icon'=>'🧾','label'=>'Order Placed'],['icon'=>'✅','label'=>'Processing'],['icon'=>'🚚','label'=>'Delivered']];
        foreach($steps as $i=>$st):
          $done=$s>$i; $active=$s===$i+1;
          $col=$done||$active?'var(--accent)':'var(--bg5)';
          $tcol=$done||$active?'var(--text)':'var(--text3)';
        ?>
        <div style="display:flex;flex-direction:column;align-items:center;flex:1">
          <div style="width:36px;height:36px;border-radius:50%;background:<?=$col?>;display:flex;align-items:center;justify-content:center;font-size:16px;transition:background .3s"><?=$st['icon']?></div>
          <div style="font-size:11px;color:<?=$tcol?>;margin-top:5px;text-align:center"><?=$st['label']?></div>
        </div>
        <?php if($i<count($steps)-1):?>
        <div style="flex:1;height:2px;background:<?=$s>$i+1?'var(--accent)':'var(--bg5)'?>;margin-bottom:18px;transition:background .3s"></div>
        <?php endif;?>
        <?php endforeach;?>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php endwhile;?>
</div>
<?php else:?>
<div class="empty-state" style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:60px">
  <div class="ei">📦</div>
  <p>No orders yet</p>
</div>
<?php endif;?>

</div></div></div>
</body></html>
