<?php require_once 'config.php';need_perm('buy');$cart=$_SESSION['cart']??[];if(!$cart){header('Location: cart.php');exit;}
$rows=[];$sub=0;foreach($cart as $id=>$n){$p=q('SELECT * FROM products WHERE id=?',[$id])->fetch();if($p){$rows[]=[$p,$n];$sub+=$p['price']*$n;}}
$co=&$_SESSION['co'];$co=$co??['step'=>1];$err='';
if($_SERVER['REQUEST_METHOD']=='POST'){check();$a=$_POST['a']??'';
 if($a=='back')$co['step']=max(1,$co['step']-1);
 if($a=='s1'){$m=$_POST['method']??'';$addr=trim($_POST['address']);$ph=trim($_POST['phone']);$cp=strtoupper(trim($_POST['coupon']));$c=null;
  if($cp)$c=q('SELECT * FROM coupons WHERE code=? AND active=1 AND (expires IS NULL OR expires>=CURDATE())',[$cp])->fetch();
  if(!isset(FEES[$m])||($m!='pickup'&&!$addr)||!preg_match('/^\d{9,12}$/',$ph))$err='Choose a delivery option, add an address (not needed for pickup) and a valid phone number.';
  elseif($cp&&!$c)$err='That coupon code is invalid or expired.';
  else $co=['step'=>2,'m'=>$m,'addr'=>$addr,'ph'=>$ph,'cp'=>$cp,'pc'=>$c['percent']??0];}
 if($a=='s2'){if(strlen(trim($_POST['txn']))<6)$err='Enter the transaction ID from your Mobile Money SMS.';else{$co['txn']=trim($_POST['txn']);$co['step']=3;}}
 if($a=='place'&&$co['step']==3){[$disc,$fee,$tot]=totals($co,$sub);$d=db();$d->beginTransaction();
  try{q('INSERT INTO orders(user_id,total,subtotal,delivery_fee,discount,method,coupon,address,phone,txn) VALUES(?,?,?,?,?,?,?,?,?,?)',[me()['id'],$tot,$sub,$fee,$disc,$co['m'],$co['cp']?:null,$co['addr'],$co['ph'],$co['txn']]);$oid=$d->lastInsertId();
   foreach($rows as [$p,$n]){if(!q('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?',[$n,$p['id'],$n])->rowCount())throw new Exception($p['name'].' is out of stock. Lower the quantity in your cart.');
    q('INSERT INTO order_items(order_id,product_id,qty,price,seller_id) VALUES(?,?,?,?,?)',[$oid,$p['id'],$n,$p['price'],$p['seller_id']]);}
   q('INSERT INTO order_events(order_id,status,note) VALUES(?,?,?)',[$oid,'pending','Order placed. Waiting for payment confirmation.']);$d->commit();unset($_SESSION['cart'],$_SESSION['co']);
   @mail(SUPPORT_EMAIL,"New order #$oid","Total ".money($tot)."\nTxn: {$co['txn']}\nPhone: {$co['ph']}");header("Location: account.php?new=$oid");exit;}
  catch(Exception $x){$d->rollBack();$err=$x->getMessage();}}}
[$disc,$fee,$tot]=totals($co,$sub);$s=$co['step'];$title='Checkout';include 'header.php';?>
<main class="page"><h2>Checkout</h2><ol class="steps"><?php foreach(['Delivery','Payment','Review'] as $i=>$n):?><li class="<?=$s>$i+1?'done':($s==$i+1?'now':'')?>"><?=$n?></li><?php endforeach;?></ol>
<?php if($err):?><p class="err"><?=e($err)?></p><?php endif;
if($s==1):?><form method="post" class="f"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="s1">
<?php foreach(FEES as $k=>$v):?><label class="opt"><input type="radio" name="method" value="<?=$k?>" <?=($co['m']??'standard')==$k?'checked':''?>> <?=e($v[0])?> <b><?=$v[1]?money($v[1]):'Free'?></b></label><?php endforeach;?>
<label>Delivery address<textarea name="address"><?=e($co['addr']??'')?></textarea></label><label>Phone for delivery<input name="phone" value="<?=e($co['ph']??'')?>" placeholder="07XXXXXXXX"></label><label>Coupon code (optional)<input name="coupon" value="<?=e($co['cp']??'')?>"></label><button>Continue to payment</button></form>
<?php else:?><div class="sum">Subtotal <?=money($sub)?><br>Discount <?=$co['cp']?e($co['cp']).' -'.$co['pc'].'% ':''?>-<?=money($disc)?><br>Delivery <?=money($fee)?><br><b class="tot">Total <?=money($tot)?></b></div>
<?php if($s==2):?><div class="pay"><b>Pay <?=money($tot)?> by Mobile Money to <?=PAY_NUMBER?></b><br>Then copy the transaction ID from the confirmation SMS.</div>
<form method="post" class="f"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="s2"><label>Transaction ID<input name="txn" required></label><button>Review order</button></form>
<?php else:?><div class="pay">Delivering to: <?=e($co['addr']?:'Depot pickup')?> · <?=e($co['ph'])?><br>Payment ID: <?=e($co['txn'])?><br><?=count($rows)?> item(s) from your cart</div>
<form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><button name="a" value="place">Confirm and place order</button></form><?php endif;?>
<form method="post" style="margin-top:1rem"><input type="hidden" name="t" value="<?=csrf()?>"><button name="a" value="back">Back</button></form><?php endif;?></main><?php include 'footer.php';
