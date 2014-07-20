<?php
    define('IN_MSG_CALL',true);
    require("LowFunction.php");
    //Flags
    $SQLIntroFlag=false;
    $UserAcceptFlag=false;
    //Die output
    $errormsg="Known Error";
    //Post value
    $uid=0;
    $name="";
    $skey="";
    $room="";
    if(SQLIntro())
    {
        $SQLIntroFlag=true;
        //check user
        if( isset($_GET['uid'])&&
            isset($_GET['name'])&& 
            isset($_GET['skey'])&&
            isset($_GET['room'])){
            
            $uid=@$_GET['uid'];
            $name=@$_GET['name'];
            $skey=@$_GET['skey'];
            $room=@$_GET['room'];$room=intval($room);
            
            if(checkUid($uid)&&MakeSKey($name,$uid)==$skey&&checkRoomAcc($uid,$room)){
                $UserAcceptFlag=true;
            }
        }
        
    }

    if(!$SQLIntroFlag)
    {
        //For Debug
        echo mysql_error();
        die ('error:SQL error');
    }
    if(!$UserAcceptFlag)
    {
        die ("error:User information error. Please <a href='http://forum.tfcis.org/' target='_BLANK'>login</a> first!");
    }
?>
<!DOCTYPE html>
<head>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="msgstyle.css">

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<script>
    var uid=<?php echo $uid;?>;
    var uname=<?php $name=htmlspecialchars($name);echo"'$name'"; ?>;
    var skey=<?php  $skey=htmlspecialchars($skey);echo"'$skey'"; ?>;
    var roomid=<?php echo $room;?>;
    var lock=false;
	window.onload = function(){
		
	}
	function do_post(){
        var content=document.getElementById("mainc").value;
        if(content==''){
            document.getElementById("errmsg").innerHTML='輸入點文字吧!';
            return false;
        }else if(lock){
            document.getElementById("errmsg").innerHTML='訊息發送中';
            return false;
        }
        document.getElementById("errmsg").innerHTML='';
        lock=true;
		$.post( 'InsertMsg.php',{
				'PostFlag':'Post',
                'room':roomid,
                'uid':uid,
				'name':uname,
				'Content':content,
				'skey':skey
				},function(res){
                    var data=JSON.parse(res);
                    if(data.status[0]=='e'){
                        document.getElementById("errmsg").innerHTML=data.info;
                    }else{
                        document.getElementById("mainc").value="";
                    }
				});
        lock=false;
		return false;
	}
	</script>
</head>
<body class='fmbdy'>
<form action='InsertMsg.php' method='POST' onsubmit="do_post();return false;">

    <input type='text' id="mainc" class="frmtb" style="height: 20px; width: 173px;">
    <input type='submit' value='送出' class="frmbtn" style="height: 23px; width: 34px;">
</form>
<div id="errmsg" style="font-size: 3pt; color:#CCCCCC"></div>
</body>