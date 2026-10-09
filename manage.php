<?php $title='Users & coupons';require_once 'config.php';need_perm('*');
if($_SERVER['REQUEST_METHOD']=='POST'){check();
 if(isset($_POST['uid'])&&(int)$_POST['uid']!=me()['id'])q('UPDATE users SET role=?,status=? WHERE id=?',[$_POST['role'],$_POST['status'],(int)$_POST['uid']]);
 if(isset($_POST['code'])&&trim($_POST['code']))q('REPLACE INTO coupons(code,percent,active) VALUES(?,?,1)',[strtoupper(trim($_POST['code'])),min(90,max(1,(int)$_POST['pct']))]);
 if(isset($_POST['tog']))q('UPDATE coupons SET active=1-active WHERE code=?',[$_POST['tog']]);
 header('Location: manage.php');exit;}
include 'header.php';?><main class="page"><h2>Users and roles</h2><table><tr><th>Name<th>Email<th>Role<th>Status<th></tr>
<?php foreach(q('SELECT * FROM users ORDER BY id DESC')->fetchAll() as $u):?><tr><form method="post"><td><?=e($u['name'])?><br><small><?=e($u['shop'])?></small><td><?=e($u['email'])?><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="uid" value="<?=$u['id']?>">
<td><select name="role"><?php foreach(['customer','seller','support','admin'] as $r):?><option <?=$u['role']==$r?'selected':''?>><?=$r?></option><?php endforeach;?></select><td><select name="status"><?php foreach(['active','pending','suspended'] as $r):?><option <?=$u['status']==$r?'selected':''?>><?=$r?></option><?php endforeach;?></select><td><button>Save</button></form></tr><?php endforeach;?></table>
<h2>Coupons</h2><form method="post" class="inline"><input type="hidden" name="t" value="<?=csrf()?>"><input name="code" placeholder="CODE"><input class="qty" name="pct" type="number" placeholder="%"><button>Add coupon</button></form>
<table><?php foreach(q('SELECT * FROM coupons')->fetchAll() as $c):?><tr><td><?=e($c['code'])?><td><?=$c['percent']?>% off<td><form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><button name="tog" value="<?=e($c['code'])?>"><?=$c['active']?'Disable':'Enable'?></button></form></tr><?php endforeach;?></table></main><?php include 'footer.php';
