<?php
require_once '../includes/config.php';
$user=auth_required();
header('Content-Type: application/json');
$action=$_GET['action']??'';

if($action==='list_tests'){
    $tests=query('SELECT * FROM mock_tests ORDER BY id DESC',[]);
    json_response(['success'=>true,'tests'=>$tests]);
}

if($action==='list_resources'){
    $cat=$_GET['category']??'all';
    if($cat==='all'){
        $res=query('SELECT * FROM cambridge_resources ORDER BY id DESC',[]);
    }else{
        $res=query('SELECT * FROM cambridge_resources WHERE category=? ORDER BY id DESC',[$cat]);
    }
    json_response(['success'=>true,'resources'=>$res]);
}

json_response(['error'=>'Invalid action'],400);
