<?php
    define('IN_MSG_CALL',true);
    require("LowFunction.php");

    $SQLIntroFlag=false;
    $UserAcceptFlag=false;
    $json=array();
    //userinfo
    $uid=0;
    $name="";
    $skey="";
    $room="";
    $content="";
    $time=date('Y-m-d G:i:s');
    if(isset($_POST["PostFlag"]))
    {
        if(SQLIntro())
        {
            $SQLIntroFlag=true;
            if( isset($_POST['uid'])&&
                isset($_POST['name'])&& 
                isset($_POST['skey'])&&
                isset($_POST['room'])&&
                isset($_POST['Content'])){
                
                $uid=@$_POST['uid'];
                $name=@$_POST['name'];
                $skey=@$_POST['skey'];
                $room=@$_POST['room'];$room=intval($room);
                $Content=@$_POST['Content'];

                if(checkUid($uid)&&MakeSKey($name,$uid)==$skey&&checkRoomAcc($uid,$room)){
                    $UserAcceptFlag=true;
                }
                //Check Content
                $sContent=mysql_real_escape_string($_POST["Content"]);
            }
        }
        
        //Insert and Make Json
        if(!$SQLIntroFlag){
            $json["status"]="esql";
            $json["info"]="Error with SQL";
        }
        else if(!$UserAcceptFlag){
            $json["status"]="einfo";
            $json["info"]="Error with user information";
        }else if(!$sContent || $sContent==''){
            $json["status"]="einput";
            $json["info"]="輸入點文字吧!";
        }
        else{
            $status=mysql_query("INSERT INTO msgdata (timestamp,roomid,userid,username,content) value('$time',$room,$uid,'$name','$sContent')");
            if($status){
                $json["status"]="yes";
                $json["info"]="ok";//$status
            }else{
                $json["status"]="esql";
                $json["info"]="Error with SQL!";
            } 
        }
        echo json_encode($json);
    }
    else
        die('ePost');
?>
