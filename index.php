<?php $title='Building materials marketplace';include 'header.php';
$s=trim($_GET['s']??'');$c=(int)($_GET['c']??0);$mx=(int)($_GET['max']??0);$near=!empty($_GET['near']);$L=loc();$w=['p.stock>0'];$a=[];
if($s){$w[]='(p.name LIKE ? OR p.descr LIKE ?)';$a[]="%$s%";$a[]="%$s%";}
if($c){$w[]='p.cat_id=?';$a[]=$c;}
if($mx>0){$w[]='p.price<=?';$a[]=$mx;}
if($near&&$L){$w[]='u.district=?';$a[]=$L;}
$o=['new'=>'p.id DESC','low'=>'p.price ASC','high'=>'p.price DESC'][$_GET['o']??'new']??'p.id DESC';
$ps=q('SELECT p.*,c.name cat,u.shop,u.district sd FROM products p LEFT JOIN categories c ON c.id=p.cat_id LEFT JOIN users u ON u.id=p.seller_id WHERE '.implode(' AND ',$w).' ORDER BY '.$o,$a)->fetchAll();
$cats=q('SELECT * FROM categories')->fetchAll();$rec=($s||$c||$mx||$near)?[]:recs(8);$st=q('SELECT COUNT(*) n,COUNT(DISTINCT seller) s FROM products')->fetch();?>
<section class="hero"><h1>Everything your site needs, from sellers near you.</h1><p>Compare prices, pay with Mobile Money, track delivery to your site.</p><p><a class="btn big" href="#shop">Shop materials</a> <a class="btn big ghost2" href="auth.php?tab=register&seller=1">Sell on BuildMart</a></p>
<div class="stats"><div><b><?=$st['n']?>+</b>Products</div><div><b><?=$st['s']?></b>Shops</div><div><b><?=count($cats)?></b>Categories</div><div><b>30</b>Districts served</div></div></section>
<?php $ic=['Cement'=>'🏗️','Steel'=>'🔩','Roofing'=>'🏠','Bricks'=>'🧱','Plumbing'=>'🚰','Electrical'=>'💡','Paint'=>'🎨','Timber'=>'🪵'];?>
<section class="band"><div class="tiles"><?php foreach($cats as $k):$e1='📦';foreach($ic as $w1=>$v1)if(stripos($k['name'],$w1)!==false)$e1=$v1;?><a class="tile" href="?c=<?=$k['id']?>#shop"><span><?=$e1?></span><?=e($k['name'])?></a><?php endforeach;?></div></section>
<?php if($rec):?><section class="band"><div class="sec"><h2><?=$L?'Recommended near '.e($L):'Popular right now'?></h2><?php if(!$L):?><small>Set your location in the top bar for local picks.</small><?php endif;?></div>
<div class="scroller"><?php foreach($rec as $p)include 'card.php';?></div></section><?php endif;?>
<main class="wrap" id="shop"><aside><h3>Categories</h3><a class="<?=!$c?'on':''?>" href="index.php">All materials</a>
<?php foreach($cats as $k):?><a class="<?=$c==$k['id']?'on':''?>" href="?c=<?=$k['id']?>"><?=e($k['name'])?></a><?php endforeach;?>
<h3>Filter</h3><form><input type="hidden" name="c" value="<?=$c?>"><input type="hidden" name="s" value="<?=e($s)?>"><select name="o"><option value="new">Newest</option><option value="low" <?=($_GET['o']??'')=='low'?'selected':''?>>Price: low to high</option><option value="high" <?=($_GET['o']??'')=='high'?'selected':''?>>Price: high to low</option></select>
<input name="max" type="number" placeholder="Max price (RWF)" value="<?=$mx?:''?>"><?php if($L):?><label><input type="checkbox" name="near" value="1" <?=$near?'checked':''?>> Only shops in <?=e($L)?></label><?php endif;?><button>Apply</button></form></aside>
<section class="grid"><?php if(!$ps):?><p>No products match. Try different filters.</p><?php endif;foreach($ps as $p)include 'card.php';?></section></main><section class="how"><h2>How BuildMart works</h2><div class="steps3"><div class="step"><i>1</i><h3>Find materials</h3><p>Search or browse shops near your district.</p></div><div class="step"><i>2</i><h3>Pay by Mobile Money</h3><p>Send payment, add the transaction ID, done.</p></div><div class="step"><i>3</i><h3>Track delivery</h3><p>Follow your order from payment to your site.</p></div></div></section><?php include 'footer.php';
