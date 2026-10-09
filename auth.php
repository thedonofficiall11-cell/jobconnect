<?php require_once 'config.php';
if(isset($_GET['out'])){session_destroy();header('Location: index.php');exit;}
$err='';if($_SERVER['REQUEST_METHOD']=='POST'){check();
 if(isset($_POST['reg'])){$em=strtolower(trim($_POST['email']));
  if(strlen($_POST['pass'])<8||!filter_var($em,FILTER_VALIDATE_EMAIL))$err='Use a valid email and a password of at least 8 characters.';
  elseif(q('SELECT id FROM users WHERE email=?',[$em])->fetch())$err='That email is already registered. Sign in instead.';
  else{q('INSERT INTO users(name,email,phone,pass,role,status,shop,district) VALUES(?,?,?,?,?,?,?,?)',[trim($_POST['name']),$em,trim($_POST['phone']),password_hash($_POST['pass'],PASSWORD_DEFAULT),($_POST['type']??'')=='seller'?'seller':'customer',($_POST['type']??'')=='seller'?'pending':'active',trim($_POST['shop']??''),province($_POST['district']??'')?$_POST['district']:null]);
   $_SESSION['u']=q('SELECT * FROM users WHERE email=?',[$em])->fetch();header('Location: index.php');exit;}}
 else{$u=q('SELECT * FROM users WHERE email=?',[strtolower(trim($_POST['email']))])->fetch();
  if($u&&password_verify($_POST['pass'],$u['pass'])){session_regenerate_id(true);$_SESSION['u']=$u;header('Location: '.(in_array($u['role'],['admin','support'])?'admin.php':($u['role']=='seller'?'seller.php':'index.php')));exit;}$err='Email or password is incorrect.';}}
$tab=(isset($_POST['reg'])||($_GET['tab']??'')=='register')?'reg':'in';$title='Sign in';include 'header.php';?>
<main class="auth"><section class="brand"><h2>Build faster with suppliers you can trust.</h2><ul><li>✓ Verified sellers across Rwanda</li><li>✓ Pay securely with Mobile Money</li><li>✓ Track every order to your site</li><li>✓ Chat with support anytime</li></ul></section>
<section class="panel"><div class="tabs"><button type="button" data-tab="in" class="<?=$tab=='in'?'on':''?>">Sign in</button><button type="button" data-tab="reg" class="<?=$tab=='reg'?'on':''?>">Create account</button></div>
<?php if($err):?><p class="err"><?=e($err)?></p><?php endif;?>
<form method="post" class="f" id="in" <?=$tab=='in'?'':'hidden'?>><input type="hidden" name="t" value="<?=csrf()?>"><label>Email<input name="email" type="email" autocomplete="email" required></label>
<label>Password<div class="pw"><input name="pass" type="password" autocomplete="current-password" required><button type="button" data-show>Show</button></div></label><button>Sign in</button></form>
<form method="post" class="f" id="reg" <?=$tab=='reg'?'':'hidden'?>><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="reg" value="1">
<div class="kind"><label><input type="radio" name="type" value="customer" <?=isset($_GET['seller'])?'':'checked'?>><b>Buyer</b><br><small>Shop and track orders</small></label><label><input type="radio" name="type" value="seller" <?=isset($_GET['seller'])?'checked':''?>><b>Seller</b><br><small>List products (approval needed)</small></label></div>
<label>Full name<input name="name" autocomplete="name" required></label><label>Email<input name="email" type="email" required></label><label>Phone<input name="phone" placeholder="07XXXXXXXX"></label><label>District (your location or shop location)<select name="district" required><option value="">Select district</option><?=dist_opts()?></select></label>
<label id="shopf" hidden>Shop name<input name="shop"></label>
<label>Password<div class="pw"><input name="pass" type="password" data-meter autocomplete="new-password" required><button type="button" data-show>Show</button></div><div class="meter"><i></i></div><small>At least 8 characters. Mix letters, numbers and symbols.</small></label><button>Create account</button></form></section></main>
<?php include 'footer.php';
