<?php
//=============================================================================================================================================
function ECHO_ANIMATE($SPEED,$WIDTH,$HEIGHT)
{
	$disp = "<script type='text/javascript'>";
	
	for($i = 3; $i < func_num_args(); ++$i)
	{
		$disp .= "
				  var image" . ($i - 2) . "=new Image()
				  image" . ($i - 2) . ".src='" . func_get_args(0)[$i] . "'";
	}
	$disp .= "</script>
			<img src=" . func_get_args(0)[3] . " name='slide' width=$WIDTH height=$HEIGHT />
			<script>
			var step=1
			var max = " . (func_num_args() - 3) . "
			function slideit(){
			if (!document.images)
			return
			document.images.slide.src=eval('image'+step+'.src')
			if (step<max)
			step++
			else
			step=1
			setTimeout('slideit()',$SPEED)
			}
			slideit()
			</script>";
	echo $disp;
}
//=============================================================================================================================================
//=============================================================================================================================================
//Echoes out each individual element in an array.
function Echo_Array($array)
{
	foreach($array as $i)
	{
		ECHO_TEXT("$i");
	}
}
//=============================================================================================================================================
//============================================================================================================================================= 
//Echoes a information into an easy to look at format for testing.
function ECHO_TEXT()
{
	if(func_num_args() > 0)
	{
		for ($i = 0; $i < func_num_args(); $i++) {
		echo "<div class='ECHO_INFORMATION'><br>" . func_get_args(0)[$i] . "<br></div>";
		}
	}
	else
	{
		echo "<div class='ECHO_INFORMATION'><br><br></div>";
	}
}
//=============================================================================================================================================
//============================================================================================================================================= 
//Echoes a information into an easy to look at format for testing.
function ECHO_BLOCK()
{
	if(func_num_args() > 0)
	{
		for ($i = 0; $i < func_num_args(); $i++) {
		echo "<div class='ECHO_INFORMATION' style='opacity:0;'><br>" . func_get_args(0)[$i] . "<br></div>";
		}
	}
	else
	{
		echo "<div class='ECHO_INFORMATION' style='opacity:0;'><br><br></div>";
	}
}
//=============================================================================================================================================
//============================================================================================================================================= 
//Echoes a information into an easy to look at format for testing.
function ECHO_BUTTON($IMG_WIDTH,$IMG_HEIGHT,$ACTION,$TEXT)
{
	$BG = "";
	if(func_num_args() == 3 || func_num_args() == 7)
		$COLOR = "white";
	else
		$COLOR = func_get_args(0)[func_num_args() - 1];
	if($COLOR == "")
	{
		$COLOR = "rgba(0,0,0,0)";
		$BG = "background:none;";
	}
	$disp = "<div class='BUTTON_IMG_WRAPPER'style='$BG width:$IMG_WIDTH;height:$IMG_HEIGHT;'>
			<div class='BUTTON_IMG_CONTENT' style='background-color:$COLOR;width:$IMG_WIDTH;height:$IMG_HEIGHT;'>
			
			<FORM METHOD='LINK' ACTION='$ACTION'>
			<INPUT TYPE='image' SRC='img_blank.png' style='position:absolute;opacity:0;' WIDTH=$IMG_WIDTH HEIGHT=$IMG_HEIGHT BORDER='0' ALT='To Goodies'>
			</FORM>
			<div class='BUTTON_IMG_CONTENT_PARTS' style='background-color:$COLOR;width:$IMG_WIDTH;height:$IMG_HEIGHT;'>";
	
		if(gettype($TEXT) == "boolean")
		{
			$icon = func_get_args(0)[4];
			$icon_w = func_get_args(0)[5];
			$icon_h = func_get_args(0)[6];
			$disp .="<div style='display:inline-block;'><img src='$icon' style='width:$icon_w;height:$icon_h;'></div>";
		}
		else
			$disp .="<p>$TEXT</p>";
		
	$disp .="</div>
			</div>
			</div>";
	echo $disp;
}
//=============================================================================================================================================
//============================================================================================================================================= 
//Echoes a information into an easy to look at format for testing.
function ECHO_ALERT($GOOD)
{
	if($GOOD)
		$str = "style='background-color:#19FF19;'";
	else
		$str = "style='background-color:#FF1919;'";

	if(sizeof(func_get_args(0)) > 1)
	{
		for ($i = 1; $i < sizeof(func_get_args(0)); $i++) {
		echo "<div class='ECHO_INFORMATION' $str><br>" . func_get_args(0)[$i] . "<br></div>";
		}
	}
	else
	{
		echo "<div class='ECHO_INFORMATION' $str><br><br></div>";
	}
}
//=============================================================================================================================================
?>