<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	session_start();
	
	/* PHPMailer library inclusions */
 	include("../libraries/phpMailer/class.phpmailer.php");
	include("../libraries/phpMailer/class.smtp.php");
	
	setlocale(LC_MONETARY, 'en_AU');
	date_default_timezone_set('Australia/Adelaide');
	
		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<head>
	<script src="inc/jquery.js" type="text/javascript"></script>
	<script type="text/javascript">jQuery.noConflict();</script>
	<link rel="stylesheet" type="text/css" href="styleswimming.css" />
	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Expense Approvals</TITLE>
	
	
</head>

<body>
<div id="header">
	<h1>Club Tools</h1>
	<h2>Reducing the pain of club administration</h2>
</div>

<div style="clear: both;">&nbsp;</div>

<div id="content">
	<div><img src="Images/Swimmer.jpg" alt="" /></div>
	
	<?php
		
		init();
		//checkSecurity("MaintainEvents.php");
		//$security = $_SESSION['security'];
		
		//if ( $security['role'] != "admin" )
		//{
		//	cancelSession();
		//}
	?>
			
	<div id="colOne">
		<div id="menu1">
			<ul>
				<li><a href="http://www.onkaswimclub.com.au/ClubTools/MainMenu.php">Home</a></li>
				<li><a href="AddExpense.php">Create New Invoice</a></li>
				<li><a href="PayInvoice.php">Pay Invoice</a></li>
				<li><a href="UpdateExpenses.php">Update an Invoice</a></li>
				<li><a href="ApproveExpense.php">Approve Expenses</a></li>
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
		<h2>Expense Approvals</h2>
		<form action="ApproveExpenses.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ( $_POST['save'] )
			{
			}
			else
			{
				listExpenses();
			}
			
			function listExpenses()
			{
				$sql = "SELECT id, created, payee, invoice_number, description, payment_due_date, ledger_category, amount, notes, image, image_type, image_length, image_name FROM expenses"}
		?>
		</form>
	</div>
	
	<div style="clear: both;">&nbsp;</div>
	
	</div>

	<div id="footer">
		<p>Copyright (c)2010 Artistan Solutions</p>
	</div>

	<script type="text/javascript"></script> 
</body>

</html>