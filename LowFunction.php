<?php
if(!defined('IN_MSG_CALL')) {
	exit('Access Denied');
}

function SQLIntro()
{
	$dbhost = '';
	$dbuser = '';
	$dbpass = '';
	$dbname = '';
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	if(!$conn){
		return false;
	}
    mysql_query("SET NAMES 'utf8'");
    mysql_select_db($dbname);
	return true;
}

function MakeSKey($username,$userid)
{
    //to do
	if(!is_string($username)||!is_string($userid)){
		return false;
	}
	return true;
}

function checkUid($uid){
	$rule = "/^[0-9]{1,5}$/";
	if(!preg_match($rule,$uid)){
		return false;
	}
    if($uid=="0"){
        return false;
    }
	return true;
}

function checkRoomAcc($uid,$roomid)
{
	if($roomid===1)return true;
	return false;
}
?>