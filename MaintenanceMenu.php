<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php include 'SecurityFunctions.php';
	session_start();
?>

<head>
<script src="inc/jquery.js" type="text/javascript"></script>
<script type="text/javascript">jQuery.noConflict();</script>
<link rel="stylesheet" type="text/css" href="styleswimming.css" />
<!--DHTML menu-->
	
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Club Tools Main Menu</TITLE>

</head>

<body>
<div id="header">
	<h1>Swimming Time Records</h1>
	<h2>Watching Progress as it Happens</h2>
</div>

<div style="clear: both;">&nbsp;</div>

<div id="content">
	<div><img src="Images/Swimmer.jpg" alt="" /></div>
	
	<?php
		
		/*
		checkSecurity("MaintenanceMenu.php");
		$security = $_SESSION['security'];
		
		if ( $security['role'] != "admin" )
		{
			cancelSession();
		}
		*/
	?>
	
	<div id="colOne">
		<div id="menu1">
			<ul>
				<li><a href="http://www.onkaswimclub.com.au/ClubTools/MainMenu.php">Home</a></li>
				<li><a href="MaintainBudgetCategories.php">Maintain Budget Categories</a></li>
				<li><a href="MaintainMeets.php">as</a></li>
				<li><a href="MaintainSwimmers.php">bss</a></li>
				<li><a href="MaintainQualifyingCategories.php">bsss/a></li>
				<li><a href="MaintainQualifyingTimes.php">ddss</a></li>
				<li><a href="MaintainUsers.php">ffdsd</a></li>
			</ul>
		</div>
		<div class="margin-news">
			<h2>News</h2>
			<p>
				<div id="NewsItem">
					<div id="NewsTitle"><a href="index.php?news&nid=1"><h1>Website created!...</h1></a></div>
					<div id="NewsDate"><a href="index.php?news&nid=1">08-05-2010</a></div>
					<div id="NewsOverview"><a href="index.php?news&nid=1">This website is still under construction, please visit us later!</a></div>
				</div>
				<div style="clear:both;"></div>
			</p>
		</div>
	</div>

	<div id="colTwo">
		<h2>Home</h2>
		<p><script type="text/javascript">ddlevelsmenu.setup("1", "topbar","0","0")</script><p><font size="4">Home</font></p><p>Under Construction...</p></p>
	</div>
	
	<div style="clear: both;">&nbsp;</div>
</div>

<div id="footer">
	<p>Copyright (c)2010 Artistan Solutions &amp; JustHost.com</p>
</div>

<script type="text/javascript"></script> 
</body>
</html>