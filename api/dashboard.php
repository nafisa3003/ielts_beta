<?php
require_once '../includes/config.php';
$user=auth_required(); $uid=$user['id'];
$action=$_GET['action']??'stats';
if($action==='stats'){
  $skills=query('SELECT skill,band_score,sessions FROM skill_progress WHERE user_id=?',[$uid]);
  $log=query('SELECT log_date FROM streak_log WHERE user_id=? AND log_date>=DATE_SUB(CURDATE(),INTERVAL 30 DAY)',[$uid]);
  json_response(['name'=>$user['name'],'current_band'=>$user['current_band'],'target_band'=>$user['target_band'],'streak'=>$user['streak'],'skills'=>$skills,'streak_days'=>array_column($log,'log_date')]);
}
if($action==='log_streak'&&$_SERVER['REQUEST_METHOD']==='POST'){
  execute('INSERT IGNORE INTO streak_log (user_id,log_date) VALUES (?,CURDATE())',[$uid]);
  execute('UPDATE users SET streak=streak+1,last_active=CURDATE() WHERE id=? AND (last_active<CURDATE() OR last_active IS NULL)',[$uid]);
  json_response(['success'=>true]);
}
json_response(['error'=>'Unknown action.'],400);
