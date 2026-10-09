<?php $title='My orders';require_once 'config.php';need_perm('buy');
if($_SERVER['REQUEST_METHOD']=='POST'){check();$o=q("SELECT id FROM orders WHERE id=? AND user_id=? AND status='pending'",[(int)$_POST['cancel'],me()['id']])->fetch();if($o)set_status($o['id'],'cancelled','Cancelled by customer');header('Location: account.php');exit;}
include 'header.php';$os=q('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC',[me()['id']])->fetchAll();?>
<main class="page"><h2>My orders</h2><?php if(isset($_GET['new'])):?><p class="ok">Order #<?=(int)$_GET['new']?> placed. We will confirm your payment shortly.</p><?php endif;
if(!$os):?><p>No orders yet. <a href="index.php">Browse materials</a></p><?php endif;
foreach($os as $o):?><details <?=isset($_GET['new'])&&$_GET['new']==$o['id']?'open':''?>><summary>#<?=$o['id']?> · <?=money($o['total'])?> · <span class="st <?=$o['status']?>"><?=$o['status']?></span></summary>
<ul class="tl"><?php foreach(q('SELECT * FROM order_events WHERE order_id=? ORDER BY id',[$o['id']])->fetchAll() as $ev):?><li><b><?=e($ev['status'])?></b> <?=e($ev['note'])?> <small><?=e($ev['created'])?></small></li><?php endforeach;?></ul>
<?php foreach(q('SELECT i.qty,p.id,p.name FROM order_items i JOIN products p ON p.id=i.product_id WHERE order_id=?',[$o['id']])->fetchAll() as $i):?><div><?=$i['qty']?> × <?=e($i['name'])?><?php if($o['status']=='delivered'):?> <a href="product.php?id=<?=$i['id']?>">Write review</a><?php endif;?></div><?php endforeach;
if($o['status']=='pending'):?><form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><button name="cancel" value="<?=$o['id']?>" onclick="return confirm('Cancel this order?')">Cancel order</button></form><?php endif;?></details><?php endforeach;?></main><?php include 'footer.php';
