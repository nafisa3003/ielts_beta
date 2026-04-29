<?php
require_once '../includes/config.php';
$user=auth_required(); $uid=$user['id'];
$skills=query('SELECT skill,band_score,sessions FROM skill_progress WHERE user_id=?',[$uid]);
$sm=[];foreach($skills as $s)$sm[$s['skill']]=['score'=>floatval($s['band_score']),'sessions'=>intval($s['sessions'])];
$tests=queryOne('SELECT COUNT(*) as cnt FROM test_attempts WHERE user_id=?',[$uid])['cnt']??0;
$cards=queryOne('SELECT COUNT(*) as cnt FROM flashcard_reviews WHERE user_id=?',[$uid])['cnt']??0;
json_response(['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'target_band'=>floatval($user['target_band']),'current_band'=>floatval($user['current_band']),'streak'=>intval($user['streak']),'skills'=>$sm,'stats'=>['tests'=>$tests,'cards'=>$cards]]);
