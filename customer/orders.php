<?php
$base_path = '../';
$active_page='orders'; $page_title='My Orders';
require '../nav.php';

$success_order = $_GET['order'] ?? '';

$orders=$conn->prepare("SELECT o.*, GROUP_CONCAT(oi.product_name, ' ×', oi.quantity SEPARATOR ', ') AS items FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id WHERE o.customer_id=? GROUP BY o.id ORDER BY o.created_at DESC");
$orders->bind_param("i",$cust_id);$orders->execute();
$orders=$orders->get_result();

function sb($s){$m=['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];return"<span class='badge ".($m[$s]??'b-gray')."'>".ucfirst($s)."</span>";}
function step($status){$steps=['pending'=>1,'completed'=>2,'delivered'=>3,'cancelled'=>0];return$steps[$status]??0;}
?>

<div class="cust-page">

<?php if($success_order):?>
<div class="alert alert-success" style="font-size:15px">
  🎉 <strong>Order placed successfully!</strong> Your order <strong><?=htmlspecialchars($success_order)?></strong> is being processed.
</div>
<?php endif;?>

<div style="margin-bottom:20px"><h2 style="font-size:20px;font-weight:800">📦 My Orders</h2></div>

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
  <p>You haven't placed any orders yet</p>
  <a href="shop.php" class="btn btn-primary" style="margin-top:16px">Start Shopping →</a>
</div>
<?php endif;?>

</div></div></div>
</body></html>
