<?php require_once 'config.php';need_perm('orders_view');$adm=me()['role']=='admin';
if($_SERVER['REQUEST_METHOD']=='POST'){check();
 if($adm&&isset($_POST['np']))q('INSERT INTO products(cat_id,name,descr,price,unit,stock,seller) VALUES(?,?,?,?,?,?,?)',[$_POST['cat'],$_POST['name'],$_POST['descr'],(int)$_POST['price'],$_POST['unit'],(int)$_POST['stock'],$_POST['seller']]);
 if($adm&&isset($_POST['del']))q('DELETE FROM products WHERE id=?',[(int)$_POST['del']]);
 if(isset($_POST['st']))set_status((int)$_POST['st'],$_POST['status'],'Updated by '.me()['role']);
 if(isset($_POST['reply'])&&trim($_POST['body']))q("INSERT INTO chat_messages(chat_id,sender,body) VALUES(?,'agent',?)",[$_POST['cid'],trim($_POST['body'])]);
 header('Location: admin.php');exit;}
$title='Admin';include 'header.php';
$os=q('SELECT o.*,u.name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 50')->fetchAll();
$cats=q('SELECT * FROM categories')->fetchAll();$ps=q('SELECT * FROM products ORDER BY id DESC')->fetchAll();
$chats=q("SELECT chat_id,MAX(id) m FROM chat_messages GROUP BY chat_id ORDER BY m DESC LIMIT 10")->fetchAll();?>
<main class="page"><h2>Orders</h2><table><tr><th>#<th>Customer<th>Total<th>Txn ID<th>Phone<th>Status</tr>
<?php foreach($os as $o):?><tr><td><?=$o['id']?><td><?=e($o['name'])?><td><?=money($o['total'])?><td><?=e($o['txn'])?><td><?=e($o['phone'])?><td>
<form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="st" value="<?=$o['id']?>"><select name="status" onchange="this.form.submit()"><?php foreach(['pending','paid','shipped','delivered','cancelled'] as $s):?><option <?=$o['status']==$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></form></tr><?php endforeach;?></table>
<?php if($adm):?><h2>Add product</h2><form method="post" class="f"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="np" value="1"><label>Name<input name="name" required></label><label>Category<select name="cat"><?php foreach($cats as $k):?><option value="<?=$k['id']?>"><?=e($k['name'])?></option><?php endforeach;?></select></label><label>Description<textarea name="descr"></textarea></label><label>Price (<?=CUR?>)<input name="price" type="number" required></label><label>Unit<input name="unit" value="piece"></label><label>Stock<input name="stock" type="number" required></label><label>Seller<input name="seller"></label><button>Add product</button></form>
<h2>Products</h2><table><?php foreach($ps as $p):?><tr><td><?=e($p['name'])?><td><?=money($p['price'])?><td><?=$p['stock']?> in stock<td><form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><button name="del" value="<?=$p['id']?>" onclick="return confirm('Delete this product?')">Delete</button></form></tr><?php endforeach;?></table>
<?php endif;?><h2>Live chats</h2><?php foreach($chats as $c):$ms=q('SELECT * FROM chat_messages WHERE chat_id=? ORDER BY id DESC LIMIT 6',[$c['chat_id']])->fetchAll();?><div class="pay"><?php foreach(array_reverse($ms) as $m):?><div><b><?=$m['sender']?>:</b> <?=e($m['body'])?></div><?php endforeach;?>
<form method="post" class="inline"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="cid" value="<?=e($c['chat_id'])?>"><input name="body" placeholder="Reply as agent"><button name="reply" value="1">Send</button></form></div><?php endforeach;?></main><?php include 'footer.php';
