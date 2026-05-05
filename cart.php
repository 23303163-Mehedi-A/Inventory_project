<?php
$active_page='cart'; $page_title='My Cart';
require 'nav.php';

$msg='';
$payment_method='cash';
$transaction_id='';

// Remove item
if(isset($_GET['remove'])){
    $pid=(int)$_GET['remove'];
    $s=$conn->prepare("DELETE FROM cart WHERE customer_id=? AND product_id=?");
    $s->bind_param("ii",$cust_id,$pid);$s->execute();
    $msg='<div class="alert alert-info">🗑️ Item removed from cart.</div>';
}

// Update quantity
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['update_qty'])){
    foreach($_POST['qty'] as $pid=>$qty){
        $pid=(int)$pid; $qty=(int)$qty;
        if($qty<1){
            $s=$conn->prepare("DELETE FROM cart WHERE customer_id=? AND product_id=?");
            $s->bind_param("ii",$cust_id,$pid);$s->execute();
        } else {
            $s=$conn->prepare("UPDATE cart SET quantity=? WHERE customer_id=? AND product_id=?");
            $s->bind_param("iii",$qty,$cust_id,$pid);$s->execute();
        }
    }
    $msg='<div class="alert alert-success">✅ Cart updated.</div>';
}

// Checkout
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['checkout'])){
    $address=trim($_POST['address']??'');
    $phone=trim($_POST['phone']??'');
    $notes=trim($_POST['notes']??'');
    $payment_method=trim($_POST['payment_method']??'cash');
    $transaction_id=trim($_POST['transaction_id']??'');

    if($payment_method==='bank_transfer' && !$transaction_id){
        $msg='<div class="alert alert-danger">⚠️ Please enter your bank transaction reference.</div>';
    }

    // Get cart items
    $ci=$conn->prepare("SELECT c.quantity, p.id AS pid, p.name, p.price, p.quantity AS stock FROM cart c JOIN products p ON p.id=c.product_id WHERE c.customer_id=?");
    $ci->bind_param("i",$cust_id);$ci->execute();
    $items=$ci->get_result()->fetch_all(MYSQLI_ASSOC);

    if(empty($items)){$msg='<div class="alert alert-danger">⚠️ Your cart is empty.</div>';}
    else{
        // Get customer info
        $cs=$conn->prepare("SELECT name,email FROM customers WHERE id=?");$cs->bind_param("i",$cust_id);$cs->execute();
        $cust=$cs->get_result()->fetch_assoc();

        $total=0; foreach($items as $it) $total+=$it['price']*$it['quantity'];
        $order_no='TM-'.date('Ymd').'-'.str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);

        $conn->begin_transaction();
        try {
            // Insert order
            $os=$conn->prepare("INSERT INTO orders (order_no,customer_id,customer_name,customer_email,customer_phone,total_price,address,notes,payment_method,transaction_id) VALUES(?,?,?,?,?,?,?,?,?,?)");
            $os->bind_param("sisssdssss",$order_no,$cust_id,$cust['name'],$cust['email'],$phone,$total,$address,$notes,$payment_method,$transaction_id);
            $os->execute();
            $oid=$conn->insert_id;

            // Insert items & reduce stock
            foreach($items as $it){
                if($it['quantity']>$it['stock']) throw new Exception("Not enough stock for ".$it['name']);
                $ois=$conn->prepare("INSERT INTO order_items (order_id,product_id,product_name,quantity,unit_price,subtotal) VALUES(?,?,?,?,?,?)");
                $sub=$it['price']*$it['quantity'];
                $ois->bind_param("iisids",$oid,$it['pid'],$it['name'],$it['quantity'],$it['price'],$sub);
                $ois->execute();
                $us=$conn->prepare("UPDATE products SET quantity=quantity-? WHERE id=?");
                $us->bind_param("ii",$it['quantity'],$it['pid']);$us->execute();
            }

            // Clear cart
            $clr=$conn->prepare("DELETE FROM cart WHERE customer_id=?");$clr->bind_param("i",$cust_id);$clr->execute();
            $conn->commit();
            header("Location: /inventory_project/customer/orders.php?success=1&order=".$order_no); exit;
        } catch(Exception $e){
            $conn->rollback();
            $msg='<div class="alert alert-danger">⚠️ '.$e->getMessage().'</div>';
        }
    }
}

// Load cart
$ci=$conn->prepare("SELECT c.quantity AS cart_qty, p.id AS pid, p.name, p.price, p.quantity AS stock, p.category FROM cart c JOIN products p ON p.id=c.product_id WHERE c.customer_id=? ORDER BY c.added_at DESC");
$ci->bind_param("i",$cust_id);$ci->execute();
$cart_items=$ci->get_result()->fetch_all(MYSQLI_ASSOC);

$total=0; foreach($cart_items as $it) $total+=$it['price']*$it['cart_qty'];

// Pre-fill address/phone from customer
$cdata=$conn->prepare("SELECT phone,address FROM customers WHERE id=?");$cdata->bind_param("i",$cust_id);$cdata->execute();
$cinfo=$cdata->get_result()->fetch_assoc();

$cat_icons=['Power Tools'=>'🔌','Hand Tools'=>'🔨','Measuring'=>'📏','Safety Gear'=>'⛑️','Electrical'=>'⚡','Other'=>'🧰'];
function cicon($c){global $cat_icons;return $cat_icons[$c]??'🧰';}
?>

<div class="cust-page">
<?=$msg?>
<div style="margin-bottom:20px"><h2 style="font-size:20px;font-weight:800">🛒 My Cart</h2></div>

<?php if(empty($cart_items)):?>
<div class="empty-state" style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:60px">
  <div class="ei">🛒</div>
  <p>Your cart is empty</p>
  <a href="shop.php" class="btn btn-primary" style="margin-top:16px">Browse Products</a>
</div>
<?php else:?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:22px;align-items:start">

  <!-- Cart Items -->
  <div>
    <div class="card" style="margin-bottom:0">
      <div class="card-header">
        <h3>Cart Items (<?=count($cart_items)?>)</h3>
        <a href="shop.php" class="btn btn-sm btn-ghost">+ Add More</a>
      </div>
      <form method="POST" id="cart-form">
        <?php foreach($cart_items as $it):?>
        <div class="cart-item">
          <div class="cart-item-icon"><?=cicon($it['category']??'')?></div>
          <div class="cart-item-info">
            <div class="cart-item-name"><?=htmlspecialchars($it['name'])?></div>
            <div class="cart-item-price">৳<?=number_format((float)$it['price'],2)?> each</div>
            <div style="font-size:11px;color:var(--text3);margin-top:2px"><?=$it['stock']?> in stock</div>
          </div>
          <div class="cart-item-qty">
            <input type="number" name="qty[<?=$it['pid']?>]" value="<?=$it['cart_qty']?>" min="0" max="<?=$it['stock']?>" style="width:64px;text-align:center"/>
          </div>
          <div class="cart-item-total">৳<?=number_format((float)$it['price']*$it['cart_qty'],2)?></div>
          <a href="cart.php?remove=<?=$it['pid']?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove item?')" style="margin-left:8px">🗑️</a>
        </div>
        <?php endforeach;?>
        <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px">
          <button type="submit" name="update_qty" class="btn btn-ghost">🔄 Update Cart</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Checkout Summary -->
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><h3>📋 Order Summary</h3></div>
      <div class="card-body">
        <?php foreach($cart_items as $it):?>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px">
          <span style="color:var(--text2)"><?=htmlspecialchars($it['name'])?> ×<?=$it['cart_qty']?></span>
          <strong>৳<?=number_format((float)$it['price']*$it['cart_qty'],2)?></strong>
        </div>
        <?php endforeach;?>
        <hr class="divider"/>
        <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800">
          <span>Total</span>
          <span style="color:var(--accent)">৳<?=number_format($total,2)?></span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>🚚 Delivery Details</h3></div>
      <form method="POST">
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
          <div class="fg">
            <label>Phone Number</label>
            <input type="tel" name="phone" placeholder="+880 1XXXXXXXXX" value="<?=htmlspecialchars($cinfo['phone']??'')?>"/>
          </div>
          <div class="fg">
            <label>Delivery Address *</label>
            <textarea name="address" placeholder="Full delivery address…" required><?=htmlspecialchars($cinfo['address']??'')?></textarea>
          </div>
          <div class="fg">
            <label>Notes (optional)</label>
            <textarea name="notes" placeholder="Any special instructions…"></textarea>
          </div>
          <div class="fg">
            <label>Payment Method</label>
            <div style="display:flex;gap:12px;margin-top:8px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="radio" name="payment_method" value="cash" checked/>
                💵 Cash on Delivery
              </label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="radio" name="payment_method" value="bank_transfer"/>
                🏦 Bank Transfer
              </label>
            </div>
          </div>
          <div id="bank-fields" style="display:none;border:1px solid var(--border);padding:12px;border-radius:var(--radius);background:var(--bg2)">
            <label>Bank Reference/Transaction ID *</label>
            <input type="text" name="transaction_id" placeholder="e.g., TXN-123456-ABC" id="txn_id"/>
            <div style="font-size:12px;color:var(--text3);margin-top:6px">
              💡 Provide your bank transaction reference so we can verify your payment.
            </div>
          </div>
        </div>
        <div style="padding:14px 16px;border-top:1px solid var(--border)">
          <button type="submit" name="checkout" class="btn btn-primary btn-full" style="font-size:15px;padding:13px">
            ✅ Place Order — ৳<?=number_format($total,2)?>
          </button>
        </div>
        <script>
          document.querySelectorAll('input[name="payment_method"]').forEach(r => {
            r.addEventListener('change', () => {
              document.getElementById('bank-fields').style.display = r.value === 'bank_transfer' ? 'block' : 'none';
              document.getElementById('txn_id').required = r.value === 'bank_transfer';
            });
          });
        </script>
      </form>
    </div>
  </div>

</div>
<?php endif;?>
</div>
</body></html>