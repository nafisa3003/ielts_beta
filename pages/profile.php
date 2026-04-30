<?php
require_once '../includes/config.php';
$user=auth_required(); $active_page='profile'; $sidebar_user=$user;
$page_title='Profile'; $extra_css=['dashboard.css']; $topbar_links='dashboard';
require_once '../includes/header.php'; require_once '../includes/sidebar.php';
$parts=explode(' ',trim($user['name']));
$initials=strtoupper(substr($parts[0],0,1)).(isset($parts[1])?strtoupper(substr($parts[1],0,1)):'');
$attempts=query('SELECT * FROM test_attempts WHERE user_id=? ORDER BY completed_at DESC LIMIT 5',[$user['id']]);
?>
<div class="app-layout">
<main class="app-main">
  <div class="pg-title" style="margin-bottom:20px;">👤 My Profile</div>
  <div class="profile-header">
    <div class="profile-avatar-wrap"><?=htmlspecialchars($initials)?></div>
    <div>
      <div style="font-family:var(--font);font-size:22px;font-weight:800;"><?=htmlspecialchars($user['name'])?></div>
      <div style="font-size:14px;color:var(--text2);margin-top:4px;"><?=htmlspecialchars($user['email'])?> · Member since <?=date('M Y',strtotime($user['created_at']))?></div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
        <span class="badge badge-gold">🥇 <?=intval($user['streak'])?> Day Streak</span>
        <span class="badge badge-green">✅ Band <?=number_format($user['target_band'],1)?> Candidate</span>
        <span class="badge badge-blue">📅 Goal: <?=($user['test_date']?date('M j Y',strtotime($user['test_date'])):'Not set')?></span>
      </div>
    </div>
  </div>

  <div class="tabs" id="profile-tabs">
    <div class="tab active" onclick="switchTab('info',this)">Personal Info</div>
    <div class="tab" onclick="switchTab('goals',this)">Goals</div>
    <div class="tab" onclick="switchTab('history',this)">Test History</div>
    <div class="tab" onclick="switchTab('settings',this)">Settings</div>
  </div>

  <!-- Personal Info -->
  <div id="ptab-info" class="card" style="max-width:500px;">
    <div class="grid2" style="gap:12px;">
      <div class="fg"><label class="fl">First name</label><input class="fi" id="fname" value="<?=htmlspecialchars($parts[0])?>"></div>
      <div class="fg"><label class="fl">Last name</label><input class="fi" id="lname" value="<?=htmlspecialchars($parts[1]??'')?>"></div>
    </div>
    <div class="fg"><label class="fl">Email</label><input class="fi" type="email" id="pemail" value="<?=htmlspecialchars($user['email'])?>"></div>
    <div class="fg"><label class="fl">Country</label><input class="fi" id="country" value="Bangladesh"></div>
    <div style="display:flex;gap:10px;margin-top:4px;">
      <button class="btn btn-primary btn-sm" onclick="saveProfile()">Save changes</button>
      <button class="btn btn-outline btn-sm" onclick="location.reload()">Cancel</button>
    </div>
  </div>

  <!-- Goals -->
  <div id="ptab-goals" class="card" style="max-width:500px;display:none;">
    <div class="fg"><label class="fl">Current band score</label>
      <select class="fi" id="current-band">
        <?php foreach([0.0,0.5,1.0,1.5,2.0,2.5,3.0,3.5,4.0,4.5,5.0,5.5,6.0,6.5,7.0,7.5,8.0,8.5,9.0] as $b): ?>
        <option value="<?=$b?>" <?=$user['current_band']==$b?'selected':''?>>Band <?=number_format($b,1)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="fg"><label class="fl">Target band score</label>
      <select class="fi" id="target-band">
        <?php foreach([5.0,5.5,6.0,6.5,7.0,7.5,8.0,8.5,9.0] as $b): ?>
        <option value="<?=$b?>" <?=$user['target_band']==$b?'selected':''?>>Band <?=number_format($b,1)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="fg"><label class="fl">Test date</label><input class="fi" type="date" id="test-date" value="<?=htmlspecialchars($user['test_date']??'')?>"></div>
    <div class="fg"><label class="fl">Daily study goal (minutes)</label><input class="fi" type="number" value="60" min="15" max="480"></div>
    <div class="fg"><label class="fl">Weakest skill</label><select class="fi"><option>Listening</option><option>Reading</option><option selected>Writing</option><option>Speaking</option></select></div>
    <button class="btn btn-primary btn-sm" style="margin-top:4px;" onclick="saveGoals()">Update goals</button>
  </div>

  <!-- Test History -->
  <div id="ptab-history" style="display:none;">
    <?php if(empty($attempts)): ?>
    <div class="card"><p style="color:var(--text2);font-size:14px;">No test attempts yet. <a class="alink" href="mock-tests.php">Take your first mock test →</a></p></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <?php foreach($attempts as $a): ?>
      <div class="test-card">
        <div class="test-num"><?=number_format($a['overall'],1)?></div>
        <div class="test-info">
          <div class="test-title">Mock Test — Band <?=number_format($a['overall'],1)?></div>
          <div class="test-meta">L:<?=number_format($a['score_l'],1)?> · R:<?=number_format($a['score_r'],1)?> · W:<?=number_format($a['score_w'],1)?> · S:<?=number_format($a['score_s'],1)?> · <?=date('M j Y',strtotime($a['completed_at']))?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Settings -->
  <div id="ptab-settings" class="card" style="max-width:500px;display:none;">
    <div style="font-weight:600;font-family:var(--font);margin-bottom:16px;">Preferences</div>
    <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;font-size:14px;cursor:pointer;">Dark mode <input type="checkbox" id="dm-profile-chk" style="accent-color:var(--teal);transform:scale(1.3);" onchange="Theme.toggle()"></label>
    <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;font-size:14px;cursor:pointer;">Email notifications <input type="checkbox" checked style="accent-color:var(--teal);transform:scale(1.3);"></label>
    <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;font-size:14px;cursor:pointer;">Streak reminders <input type="checkbox" checked style="accent-color:var(--teal);transform:scale(1.3);"></label>
    <hr style="border:none;border-top:1px solid var(--border);margin-bottom:16px;">
    <div class="fg"><label class="fl">Change password</label><input class="fi" type="password" id="new-pw" placeholder="New password (min 6 chars)"></div>
    <div style="display:flex;gap:10px;"><button class="btn btn-primary btn-sm" onclick="changePw()">Update password</button><button class="btn btn-danger btn-sm" onclick="if(confirm('Delete your account? This cannot be undone.'))deleteAccount()">Delete account</button></div>
  </div>
</main>
</div>
<?php
$inline_js="
function switchTab(name,el){
  document.querySelectorAll('[id^=ptab-]').forEach(t=>t.style.display='none');
  document.getElementById('ptab-'+name).style.display='';
  document.querySelectorAll('#profile-tabs .tab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
}
async function saveProfile(){
  const name=document.getElementById('fname').value+' '+document.getElementById('lname').value;
  const r=await fetch('../api/profile.php?action=update',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({name})});
  const d=await r.json(); Toast.show(d.success?'Profile saved!':d.error,'success');
}
async function saveGoals(){
  const band=document.getElementById('target-band').value;
  const current=document.getElementById('current-band').value;
  const date=document.getElementById('test-date').value;
  const r=await fetch('../api/profile.php?action=update',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({target_band:band,current_band:current,test_date:date})});
  const d=await r.json(); Toast.show(d.success?'Goals updated!':d.error,'success');
}
async function changePw(){
  const pw=document.getElementById('new-pw').value;
  if(pw.length<6){Toast.show('Password must be 6+ characters','error');return;}
  const r=await fetch('../api/profile.php?action=change_password',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({new_password:pw})});
  const d=await r.json(); Toast.show(d.success?'Password updated!':d.error,d.success?'success':'error');
}
async function deleteAccount(){
  await fetch('../api/profile.php?action=delete',{method:'DELETE'});
  window.location.href='home.php';
}
";
require_once '../includes/footer.php'; ?>
