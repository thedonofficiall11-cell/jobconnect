<?php require_once 'config.php';header('Content-Type: application/json');
$cid=substr(session_id(),0,40);$last=(int)($_GET['since']??0);
if($_SERVER['REQUEST_METHOD']=='POST'){
 $m=trim(substr(json_decode(file_get_contents('php://input'),true)['msg']??'',0,500));
 if($m!==''){q("INSERT INTO chat_messages(chat_id,sender,body) VALUES(?,'user',?)",[$cid,$m]);
  $l=strtolower($m);$r=null;
  if(preg_match('/agent|human|person|support/',$l))$r='I have alerted our team. An agent will reply here shortly, or email '.SUPPORT_EMAIL.'.';
  elseif(preg_match('/pay|momo|mobile money|mtn/',$l))$r='Pay by Mobile Money to '.PAY_NUMBER.', then enter the transaction ID at checkout. We confirm and dispatch.';
  elseif(preg_match('/deliver|shipping|transport/',$l))$r='We deliver to your site. Delivery cost and timing are confirmed with your order.';
  elseif(preg_match('/order|track|status/',$l))$r='Open "Orders" in the menu to see your order status: pending, paid or delivered.';
  elseif(preg_match('/^(hi|hello|hey)/',$l))$r='Hello! Ask me about a material (e.g. cement, rebar, roofing), payment or delivery.';
  else{$w=array_filter(explode(' ',preg_replace('/[^a-z0-9 ]/','',$l)),fn($x)=>strlen($x)>2);$f=[];
   foreach($w as $x){foreach(q('SELECT name,price,unit FROM products WHERE stock>0 AND name LIKE ? LIMIT 3',["%$x%"])->fetchAll() as $p)$f[$p['name']]=$p['name'].' – '.money($p['price']).'/'.$p['unit'];}
   $r=$f?'I found: '.implode('; ',array_slice($f,0,3)).'. Open the shop to add to cart.':'I could not find that. Type "agent" to talk to a person.';}
  q("INSERT INTO chat_messages(chat_id,sender,body) VALUES(?,'bot',?)",[$cid,$r]);}}
echo json_encode(q('SELECT id,sender,body FROM chat_messages WHERE chat_id=? AND id>? ORDER BY id',[$cid,$last])->fetchAll());
