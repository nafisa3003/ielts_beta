<?php
require_once '../includes/config.php';
$user=auth_required(); $active_page='chatbot'; $sidebar_user=$user;
$page_title='AI Tutor'; $extra_css=['dashboard.css']; $topbar_links='dashboard';
require_once '../includes/header.php'; require_once '../includes/sidebar.php';
?>
<div class="app-layout">
<main class="app-main">
  <div class="pg-header">
    <div><div class="pg-title">🤖 AI Tutor</div><div class="pg-sub">Powered by LangGraph + Groq (llama-3.3-70b) + Tavily Search</div></div>
    <div class="chat-status"><span class="chat-dot" id="status-dot"></span><span id="status-text">Connecting…</span></div>
  </div>

  <div class="chat-wrap">
    <div class="chat-msgs" id="chat-msgs">
      <div class="msg bot">👋 Hi <?=htmlspecialchars(explode(' ',$user['name'])[0])?>! I'm your IELTS AI tutor, powered by LangGraph and Groq. I can help with Writing Task 2 feedback, vocabulary, grammar, test strategy, and I can search the web for the latest IELTS info. What would you like help with?</div>
    </div>
    <div class="chat-row">
      <input id="chat-input" placeholder="Ask anything about IELTS…" onkeydown="if(event.key==='Enter'&&!event.shiftKey){sendMsg();event.preventDefault();}">
      <button class="btn btn-primary btn-sm" id="send-btn" onclick="sendMsg()">Send ↑</button>
    </div>
  </div>

  <div class="quick-chips" style="margin-top:14px;">
    <div class="quick-chip" onclick="quickAsk('How do I improve my Writing Task 2 band score?')">✍️ Task 2 tips</div>
    <div class="quick-chip" onclick="quickAsk('What are common mistakes in Speaking Part 2?')">🎤 Speaking Part 2</div>
    <div class="quick-chip" onclick="quickAsk('Give me 5 academic vocabulary words for environment essays')">📖 Vocab for essays</div>
    <div class="quick-chip" onclick="quickAsk('What is the difference between Academic and General IELTS?')">❓ Academic vs General</div>
    <div class="quick-chip" onclick="quickAsk('What are the latest IELTS test dates and fees?')">🌐 Test dates & fees</div>
  </div>
</main>
</div>

<?php
$thread_id = 'user_'.$user['id'].'_'.date('Ymd');
$inline_js = "
const THREAD_ID='".htmlspecialchars($thread_id)."';
const CHATBOT_URL='http://localhost:8000/chat';
let isLoading=false;

async function checkStatus(){
  try{
    const r=await fetch('http://localhost:8000/health',{signal:AbortSignal.timeout(2000)});
    const ok=r.ok;
    document.getElementById('status-dot').className='chat-dot'+(ok?'':' offline');
    document.getElementById('status-text').textContent=ok?'AI Tutor online':'AI Tutor offline — start FastAPI server';
  }catch{
    document.getElementById('status-dot').className='chat-dot offline';
    document.getElementById('status-text').textContent='AI Tutor offline — run: uvicorn api:app --reload';
  }
}

function appendMsg(role,text){
  const msgs=document.getElementById('chat-msgs');
  const div=document.createElement('div');
  div.className='msg '+(role==='user'?'user':'bot');
  div.textContent=text;
  msgs.appendChild(div);
  msgs.scrollTop=msgs.scrollHeight;
  return div;
}

async function sendMsg(){
  if(isLoading)return;
  const inp=document.getElementById('chat-input');
  const text=inp.value.trim();
  if(!text)return;
  inp.value=''; isLoading=true;
  document.getElementById('send-btn').disabled=true;
  appendMsg('user',text);
  const thinking=appendMsg('bot','🤔 Thinking…');
  thinking.classList.add('thinking');
  try{
    const res=await fetch(CHATBOT_URL,{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({message:text,thread_id:THREAD_ID})
    });
    const data=await res.json();
    thinking.remove();
    appendMsg('bot',data.reply||data.error||'No response.');
  }catch(e){
    thinking.remove();
    appendMsg('bot','⚠️ Could not reach the AI Tutor server. Make sure FastAPI is running on port 8000.');
  }
  isLoading=false;
  document.getElementById('send-btn').disabled=false;
}

function quickAsk(q){
  document.getElementById('chat-input').value=q;
  sendMsg();
}

checkStatus();
setInterval(checkStatus, 15000);
";
require_once '../includes/footer.php'; ?>
