<?php
    define('IN_MSG_CALL',true);
    require("LowFunction.php");
    
    function getLastUid($room){
        $sqldata = mysql_query("SELECT sid FROM msgdata WHERE roomid=$room ORDER BY sid DESC LIMIT 1");
        if(!$sqldata){
            return false;
        }
        $row = mysql_fetch_array($sqldata);
        if(!$row){
            return false;
        }
        return $row['sid'];
    }
    
    $SQLIntroFlag=false;
    $UserAcceptFlag=false;
    $json=array();
    //userinfo
    $uid=0;
    $name="";
    $skey="";
    $room="";
    $lastSid=-1;
    if(SQLIntro())
    {
        $SQLIntroFlag=true;
        
        if( isset($_POST['uid'])&&
            isset($_POST['name'])&& 
            isset($_POST['skey'])&&
            isset($_POST['room'])||true){
            
            $uid=@$_POST['uid'];
            $name=@$_POST['name'];
            $skey=@$_POST['skey'];
            $room=@$_POST['room'];$room=intval($room);

            if(checkUid($uid)&&MakeSKey($name,$uid)==$skey&&checkRoomAcc($uid,$room)){
                $UserAcceptFlag=true;
            }
            
            if(isset($_POST['lastSid'])){
                $lastSid=$_POST['lastSid'];$lastSid=intval($lastSid);
                if($lastSid<0){
                    $lastSid=-1;
                }
            }
        }
    }
    //Error :  Make Json
    if(!$SQLIntroFlag){
        $json["status"]="esql";
        $json["info"]="Error with SQL";
        echo json_encode($json);exit;
    }
    else if(!$UserAcceptFlag){
        $json["status"]="einfo";
        $json["info"]="Error with user information";
        echo json_encode($json);exit;
    }
    
    $sid=getLastUid($room);
    if(!$sid){
        $json["status"]="esql";
        $json["info"]=-1;
        echo json_encode($json);exit;
    }
    //get Lastest uid
    if($lastSid<0)
    {
        $json["status"]="yes";
        $json["info"]=$sid;
        echo json_encode($json);exit;
    }
    if($lastSid>$sid)
    {
        $json["status"]="esid";
        $json["info"]=$sid;
        echo json_encode($json);exit;
    }
    //Long polling
    for($i=0;$i<30;$i++)
    {
        if($i>0){//bad
            $sid=getLastUid($room);
        }
        if(!$sid){
            $json["status"]="esql";
            $json["info"]=-1;
            echo json_encode($json);exit;
        }
        if($sid>$lastSid)
        {
            $sqldata = mysql_query("SELECT * FROM msgdata WHERE roomid=$room AND sid>$lastSid");
            $json["status"]="yes";
            $var=0;
            $json["info"]["data"]=array();
            while($row = mysql_fetch_array($sqldata))
            {
                $json["info"]["data"][$var]["sid"]=$row['sid'];
                $json["info"]["data"][$var]["timestamp"]=$row['timestamp'];
                $json["info"]["data"][$var]["userid"]=$row['userid'];
                $json["info"]["data"][$var]["username"]=htmlspecialchars($row['username']);
                $json["info"]["data"][$var]["content"]=nl2br(htmlspecialchars($row['content']));
                $var++;
            }
            echo json_encode($json);exit;
        }
        sleep(1);
    }
    //end
    $json["status"]="yes";
    $json["info"]["data"]=array();
    echo json_encode($json);exit;