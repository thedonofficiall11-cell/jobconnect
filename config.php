<?php
// ===== SETTINGS =====
const DB_HOST='localhost', DB_NAME='buildmart', DB_USER='root', DB_PASS='';
const SITE='BuildMart', PAY_NUMBER='0783310479', SUPPORT_EMAIL='thedonofficiall11@gmail.com', CUR='RWF';
session_start();
function db():PDO{static $p;if(!$p)$p=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);return $p;}
function q($sql,$a=[]){$s=db()->prepare($sql);$s->execute($a);return $s;}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function money($n){return CUR.' '.number_format($n);}
function me(){return $_SESSION['u']??null;}
function need($role=null){if(!me()){header('Location: auth.php');exit;}if($role&&me()['role']!==$role){http_response_code(403);exit('Forbidden');}}
function csrf(){return $_SESSION['t']??($_SESSION['t']=bin2hex(random_bytes(16)));}
function check(){if(($_POST['t']??'')!==csrf()){http_response_code(400);exit('Bad token');}}
function cart_count(){return array_sum($_SESSION['cart']??[]);}
// ===== ROLES & PERMISSIONS =====
const PERMS=['customer'=>['buy','review'],'seller'=>['buy','review','sell'],'support'=>['orders_view','chat'],'admin'=>['*']];
const FEES=['pickup'=>['Pick up at depot',0],'standard'=>['Standard delivery, 2-3 days',5000],'express'=>['Express delivery, same day',12000]];
function can($p){$a=PERMS[$_SESSION['u']['role']??'']??[];return in_array('*',$a)||in_array($p,$a);}
function need_perm($p){if(!me()){header('Location: auth.php');exit;}if(!can($p)){http_response_code(403);exit('Your account role cannot open this page.');}}
function totals($co,$sub){$d=(int)round($sub*($co['pc']??0)/100);$f=FEES[$co['m']??'pickup'][1];return [$d,$f,$sub-$d+$f];}
function set_status($id,$st,$note=''){$o=q('SELECT status FROM orders WHERE id=?',[$id])->fetch();if(!$o||$o['status']==$st)return;
 if($st=='cancelled')foreach(q('SELECT product_id,qty FROM order_items WHERE order_id=?',[$id])->fetchAll() as $i)q('UPDATE products SET stock=stock+? WHERE id=?',[$i['qty'],$i['product_id']]);
 q('UPDATE orders SET status=? WHERE id=?',[$st,$id]);q('INSERT INTO order_events(order_id,status,note) VALUES(?,?,?)',[$id,$st,$note]);}
if(!empty($_SESSION['u'])){$u=q('SELECT * FROM users WHERE id=?',[$_SESSION['u']['id']])->fetch();if(!$u||$u['status']=='suspended'){session_destroy();header('Location: auth.php');exit;}$_SESSION['u']=$u;}
// ===== LOCATION, RECOMMENDATIONS, UPLOADS =====
const REGIONS=['Kigali'=>['Gasabo','Kicukiro','Nyarugenge'],'Northern'=>['Burera','Gakenke','Gicumbi','Musanze','Rulindo'],'Southern'=>['Gisagara','Huye','Kamonyi','Muhanga','Nyamagabe','Nyanza','Nyaruguru','Ruhango'],'Eastern'=>['Bugesera','Gatsibo','Kayonza','Kirehe','Ngoma','Nyagatare','Rwamagana'],'Western'=>['Karongi','Ngororero','Nyabihu','Nyamasheke','Rubavu','Rusizi','Rutsiro']];
function province($d){foreach(REGIONS as $p=>$l)if(in_array($d,$l,true))return $p;return null;}
function loc(){return $_SESSION['loc']??(me()['district']??null);}
function dist_opts($sel=''){$h='';foreach(REGIONS as $p=>$l){$h.="<optgroup label=\"$p\">";foreach($l as $d)$h.='<option'.($d==$sel?' selected':'').'>'.$d.'</option>';$h.='</optgroup>';}return $h;}
function near($sd){$d=loc();if(!$d||!$sd)return '';if($sd==$d)return 'In '.$d;return province($sd)==province($d)?province($sd).' Province':'';}
function recs($n=8,$cat=0,$ex=0){$d=loc();$pl=($d&&province($d))?REGIONS[province($d)]:[];$ph=$pl?implode(',',array_fill(0,count($pl),'?')):'NULL';
 $sql="SELECT p.*,c.name cat,u.shop,u.district sd,(SELECT COALESCE(SUM(qty),0) FROM order_items WHERE product_id=p.id) sold FROM products p LEFT JOIN categories c ON c.id=p.cat_id LEFT JOIN users u ON u.id=p.seller_id WHERE p.stock>0 AND (?=0 OR p.cat_id=?) AND p.id<>? ORDER BY COALESCE(u.district=?,0) DESC,COALESCE(u.district IN ($ph),0) DESC,sold DESC,p.id DESC LIMIT ".(int)$n;
 return q($sql,array_merge([$cat,$cat,$ex,$d??''],$pl))->fetchAll();}
function save_img($f){if(empty($f['tmp_name'])||($f['error']??1)!==0||$f['size']>2097152)return null;$i=@getimagesize($f['tmp_name']);$x=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$i?$i['mime']:'']??null;if(!$x)return null;$n=bin2hex(random_bytes(8)).".$x";return move_uploaded_file($f['tmp_name'],__DIR__."/uploads/$n")?$n:null;}
