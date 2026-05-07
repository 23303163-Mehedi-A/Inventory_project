<?php
$page_title='Customers'; $active_nav='customers';
require 'layout.php';

// ── STAFF GUARD ──────────────────────────────────────────────────────────────
if ($user_role === 'staff') {
    header("Location: orders.php"); exit;
}
// ─────────────────────────────────────────────────────────────────────────────

$msg='';
if(isset($_GET['toggle'])){
    $cid=(int)$_GET['toggle'];
    $cur=$conn->prepare("SELECT status FROM customers WHERE id=?");$cur->bind_param("i",$cid);$cur->execute();
    $row=$cur->get_result()->fetch_assoc();
    $new=$row['status']==='active'?'inactive':'active';
    $s=$conn->prepare("UPDATE customers SET status=? WHERE id=?");$s->bind_param("si",$new,$cid);$s->execute();
    $msg="Customer ".($new==='active'?'activated':'deactivated').".";
}

$search=trim($_GET['search']??'');
$where="WHERE 1=1"; $params=[]; $types="";
if($search){$like="%$search%";$where.=" AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";$params=[$like,$like,$like];$types="sss";}
$stmt=$conn->prepare("SELECT c.*, (SELECT COUNT(*) FROM orders o WHERE o.customer_id=c.id) AS total_orders, (SELECT SUM(o.total_price) FROM orders o WHERE o.customer_id=c.id AND o.status!='cancelled') AS total_spent FROM customers c $where ORDER BY c.created_at DESC");
if($types) $stmt->bind_param($types,...$params);
$stmt->execute(); $customers=$stmt->get_result();
?>

<div class="page-header"><h2>👥 Customers</h2></div>
<?php if($msg):?><div class="alert alert-success">✅ <?=htmlspecialchars($msg)?></div><?php endif;?>

<div class="search-row">
  <form method="GET" style="display:flex;gap:10px">
    <div class="search-input-wrap"><span class="search-icon">🔍</span><input type="text" name="search" placeholder="Search customers…" value="<?=htmlspecialchars($search)?>"/></div>
    <button type="submit" class="btn btn-ghost">Search</button>
    <?php if($search):?><a href="customers.php" class="btn btn-ghost">✕ Clear</a><?php endif;?>
  </form>
</div>

<div class="card"><div class="tbl-wrap"><table>
  <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Spent</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
  <tbody>
    <?php if($customers->num_rows>0):$i=1; while($c=$customers->fetch_assoc()):?>
    <tr>
      <td class="td-id"><?=$i++?></td>
      <td><strong><?=htmlspecialchars($c['name'])?></strong></td>
      <td style="color:var(--blue)"><?=htmlspecialchars($c['email'])?></td>
      <td class="text-muted"><?=htmlspecialchars($c['phone']??'—')?></td>
      <td><span class="badge b-blue"><?=(int)$c['total_orders']?> orders</span></td>
      <td><strong>৳<?=number_format((float)($c['total_spent']??0),2)?></strong></td>
      <td><span class="badge <?=$c['status']==='active'?'b-green':'b-red'?>"><?=$c['status']?></span></td>
      <td style="color:var(--text2);font-size:12px"><?=date('d M Y',strtotime($c['created_at']))?></td>
      <td><a href="customers.php?toggle=<?=$c['id']?>" class="btn btn-sm btn-ghost"><?=$c['status']==='active'?'Deactivate':'Activate'?></a></td>
    </tr>
    <?php endwhile; else:?><tr><td colspan="9" class="empty-state"><div class="ei">👥</div>No customers yet</td></tr><?php endif;?>
  </tbody>
</table></div></div>

</div></div></div>
</body></html>