<?php require_once 'config.php';
if($_SERVER['REQUEST_METHOD']=='POST'){check();
 if(isset($_POST['add']))$_SESSION['cart'][(int)$_POST['add']]=($_SESSION['cart'][(int)$_POST['add']]??0)+max(1,(int)$_POST['qty']);
 if(isset($_POST['upd']))foreach($_POST['upd'] as $id=>$n){if((int)$n>0)$_SESSION['cart'][(int)$id]=(int)$n;else unset($_SESSION['cart'][(int)$id]);}
 header('Location: cart.php');exit;}
$title='Cart';include 'header.php';$items=[];$tot=0;
foreach($_SESSION['cart']??[] as $id=>$n){$p=q('SELECT * FROM products WHERE id=?',[$id])->fetch();if($p){$p['n']=$n;$tot+=$n*$p['price'];$items[]=$p;}}?>
<main class="page"><h2>Your cart</h2><?php if(!$items):?><p>Your cart is empty. <a href="index.php">Browse materials</a></p><?php else:?>
<form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><table><tr><th>Item<th>Price<th>Qty<th>Subtotal</tr>
<?php foreach($items as $p):?><tr><td><?=e($p['name'])?><td><?=money($p['price'])?><td><input class="qty" type="number" min="0" name="upd[<?=$p['id']?>]" value="<?=$p['n']?>"><td><?=money($p['n']*$p['price'])?></tr><?php endforeach;?></table>
<p class="tot">Total: <?=money($tot)?></p><button>Update cart</button> <a class="btn" href="checkout.php">Checkout</a></form><?php endif;?></main><?php include 'footer.php';
