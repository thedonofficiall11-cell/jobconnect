(()=>{const $=i=>document.getElementById(i);let last=0,timer;
const draw=a=>a.forEach(m=>{last=Math.max(last,m.id);const d=document.createElement('div');d.className='m '+m.sender;d.textContent=m.body;$('msgs').append(d);$('msgs').scrollTop=1e9;});
const poll=()=>fetch('chat.php?since='+last).then(r=>r.json()).then(draw).catch(()=>{});
$('chatBtn').onclick=()=>{const b=$('chatBox');b.hidden=!b.hidden;if(!b.hidden){poll();timer=setInterval(poll,2500);if(!last)draw([{id:0,sender:'bot',body:'Hi! Ask about materials, payment or delivery.'}]);}else clearInterval(timer);};
$('chatForm').onsubmit=e=>{e.preventDefault();const v=$('chatIn').value.trim();if(!v)return;$('chatIn').value='';
fetch('chat.php?since='+last,{method:'POST',body:JSON.stringify({msg:v})}).then(r=>r.json()).then(draw);};})();
