<footer><div class="fcols"><div><b class="flogo">Build<b>Mart</b></b><p>Verified building materials from sellers across Rwanda, delivered to your site.</p></div>
<div><h4>Shop</h4><?php foreach(q('SELECT * FROM categories LIMIT 5')->fetchAll() as $k):?><a href="index.php?c=<?=$k['id']?>"><?=e($k['name'])?></a><?php endforeach;?></div>
<div><h4>Account</h4><a href="cart.php">Cart</a><a href="account.php">My orders</a><a href="auth.php?tab=register&seller=1">Become a seller</a></div>
<div><h4>Support</h4><a href="mailto:<?=SUPPORT_EMAIL?>"><?=SUPPORT_EMAIL?></a><span>Mobile Money: <?=PAY_NUMBER?></span></div></div><div class="fbar">© <?=date('Y')?> BuildMart. All rights reserved.</div></footer>
<div id="chat"><button id="chatBtn">Chat with us</button><div id="chatBox" hidden><div class="ch">BuildMart assistant</div><div id="msgs"></div><form id="chatForm"><input id="chatIn" placeholder="Ask about products, delivery, payment…" autocomplete="off"><button>Send</button></form></div></div>
<button id="top" type="button" aria-label="Back to top">↑</button><script src="assets/app.js"></script><script src="assets/chat.js"></script></body></html>
