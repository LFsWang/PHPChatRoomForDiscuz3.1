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
        die ("error:User information error. Please login first!");
    }

    //All is ok ,get room data
    //$sqldata=mysql_query("SELECT * FROM msgdata WHERE roomid=1 ORDER BY sid DESC");
    
    $sqldata=mysql_query("SELECT * FROM msgdata WHERE roomid=1 ORDER BY sid DESC LIMIT 20");
    if(!$sqldata){
        die("<p>存取被拒</p>");
	}
?>

<!DOCTYPE html>
<head>
	<meta charset='utf-8'>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<link rel="stylesheet" href="msgstyle.css">
</head>
<body onload="location.href='#ButtonFlag'" class='mnbdy'>
<table border="0" cellpadding="2" cellspacing="0" width="100%" class="hbtbl" id="tab">
	<tbody>
	<?php
		$output="";
        $lastsid=-1;
		if($sqldata!=false)
		{
			while($row = mysql_fetch_array($sqldata))
			{
                if($row['sid']>$lastsid){
                    $lastsid=$row['sid'];
                }
				$LineData="<tr><td>";
				$LineData=$LineData."<div class='dtxt'>".$row['timestamp']."</div>".
						"<a href='http://forum.tfcis.org/home.php?mod=space&uid=$row[userid]' target='_BLANK'><b>".htmlspecialchars($row["username"])."</b></a>: ".nl2br(htmlspecialchars($row["content"]));
				$LineData=$LineData."</td></tr>";
				$output=$LineData.$output;
			}
			echo $output;
		}
	?>
	</tbody>
</table>
<a name="ButtonFlag"></a>
<script>
var lastsid=<?php echo $lastsid; ?>;
var uid=<?php echo $uid;?>;
var uname=<?php $name=htmlspecialchars($name);echo"'$name'"; ?>;
var skey=<?php  $skey=htmlspecialchars($skey);echo"'$skey'"; ?>;
var roomid=<?php echo $room;?>;
function addmsg(element, index, array){
    lastsid=Math.max(lastsid,element.sid);
    html="<div class='dtxt'>"+element.timestamp+"</div><a href='http://forum.tfcis.org/home.php?mod=space&uid="+element.userid+"' target='_BLANK'><b>"+element.username+"</b></a>: "+element.content;
    //alert(html);
    //insert
    var trnum = document.getElementById("tab").rows.length;
    var Tr = document.getElementById("tab").insertRow(trnum);
    Td = Tr.insertCell(Tr.cells.length);
    Td.innerHTML=html;
    //alert(element.sid);
}
function update(){
    var postdata={uid:uid,name:uname,skey:skey,room:roomid,lastSid:lastsid};
    $.ajax({
        url:"UpdateMsg.php",
        type: "POST",
        data: postdata,
        dataType: "json",
        timeout: 600000,
        success : function(res){
                    //alert(res.status);
                    res.info.data.forEach(addmsg);
                    location.href='#ButtonFlag';
                    update();
                },
        error:function(x,t,m){
            update();
        }
    });
}
update();
</script>
</body>