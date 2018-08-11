<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	include 'IncomeItem.php';
	include 'FundraisingCampaign.php';
	session_start();
	
	setlocale(LC_MONETARY, 'en_AU');
	date_default_timezone_set('Australia/Adelaide');		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<head>
	<script src="inc/jquery.js" type="text/javascript"></script>
	<script type="text/javascript">jQuery.noConflict();</script>
	<link rel="stylesheet" type="text/css" href="styleswimming.css" />
	<link rel="stylesheet" type="text/css" href="datepicker.css" /> 
	<script type="text/javascript" src="datepicker.js"></script>
	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Create an Income Item</TITLE>
	
	<script>
		function setFocusToTextBox(){
			document.getElementById("incomeDate").focus();
		}
	</script>
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
		$_SESSION['databaseConnection'] = mysqli_connect("localhost", "onkaswim_toolusr", "Mclarenf1", "onkaswim_clubtoolstest") or die(mysql_error());
		echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
		echo "<br>+USING THE onkaswim_clubtoolstest database+";
		echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
		
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
				<li><a href="AddIncomeItem.php">Add Income Item</a></li>
				<li><a href="AddReceipt.php">Add Receipt</a></li>
				<li><a href="UpdateIncomeItem.php">Update Income Item</a></li>
				<li><a href="UpdateReceipt.php">Update Receipt</a></li>
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
		<h2>Create an Income Item</h2>
		<form action="AddIncomeItem-New.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['save'])
			{
				$_SESSION['newIncomeItem'] = createIncomeItemFromPost();
				if ($_SESSION['newIncomeItem']->Validate())
				{
					if (isFundraisingCategory($_SESSION['newIncomeItem']->GetCategory()))
					{
						$_SESSION['workflow']['fundraisingNeeded'] = true;
					}
				
					if ($_SESSION['workflow']['followupNeeded'] && !$_SESSION['workflow']['followupCompleted'])
					{
						showBlankFollowupForm();
						echo "<input type=\"submit\" name=\"savefollowup\" value=\"Save\">";
						echo "<input type=\"submit\" name=\"cancelfollowup\" value=\"Cancel\">";
					}
					
					if ($_SESSION['workflow']['fundraisingNeeded'] && !$_SESSION['workflow']['fundraisingCompleted'])
					{
						// Choose fundraising campaign form
					}
					
					if (isWorkflowCompleted())
					{
						$_SESSION['newIncomeItem']->Save();
					}
				}
				else
				{
					showEditIncomeItemForm();
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
					echo "<input type=\"submit\" name=\"saveandreceipt\" value=\"Save and Create Receipt\">";
				}
				
				
					
				
				
			}
			elseif ($_POST['savefollowup'])
			{
				$followUp = createFollowupFromPost();
				
				if ($followUp->Validate())
				{
					$_SESSION['newIncomeItem']->AddFollowupItem($followUp);
					$_SESSION['newIncomeItem']->Save();
				}
				else
				{
					showEditFollowupForm($followUp);
					echo "<input type=\"submit\" name=\"savefollowup\" value=\"Save\">";
					echo "<input type=\"submit\" name=\"cancelfollowup\" value=\"Cancel\">";
				}
			}
			elseif ($_POST['cancelfollowup'])
			{
				$_SESSION['newIncomeItem']->ClearFollowupItems();
				showEditIncomeItemForm();
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"saveandreceipt\" value=\"Save and Create Receipt\">";
			}
			elseif ($_POST['saveandreceipt'])
			{
				
			}
			else
			{
				unset($_SESSION['newIncomeItem']);
				unset($_SESSION['fundraising']);
				initialiseWorkflow();
				
				showBlankIncomeForm();
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"saveandreceipt\" value=\"Save and Create Receipt\">";
			}
			
			function initialiseWorkflow()
			{
				unset($_SESSION['workflow']);
				$_SESSION['workflow']['followupNeeded'] = false;
				$_SESSION['workflow']['followupCompleted'] = false;
				$_SESSION['workflow']['fundraisingNeeded'] = false;
				$_SESSION['workflow']['fundraisingCompleted'] = false;
				$_SESSION['workflow']['receiptNeeded'] = false;
				$_SESSION['workflow']['receiptCompleted'] = false;
				$_SESSION['workflow']['trainingPaidNeeded'] = false;
				$_SESSION['workflow']['trainingPaidCompleted'] = false;
			}
			
			function isWorkflowCompleted()
			{
				$completed = true;
				
				if ($_SESSION['workflow']['followupNeeded'] && !$_SESSION['workflow']['followupCompleted'])
				{
					$completed = false;
				}
				
				if ($_SESSION['workflow']['fundraisingNeeded'] && !$_SESSION['workflow']['fundraisingCompleted'])
				{
					$completed = false;
				}
				
				if ($_SESSION['workflow']['receiptNeeded'] && !$_SESSION['workflow']['receiptCompleted'])
				{
					$completed = false;
				}
				
				if ($_SESSION['workflow']['trainingPaidNeeded'] && !$_SESSION['workflow']['trainingPaidCompleted'])
				{
					$completed = false;
				}
				
				return $completed;
			}
			
			function showBlankIncomeForm()
			{
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Date</td>";
					echo "<td><input id='incomeDate' name='incomeDate' value = '" . date('d-M-Y', time()) . "' class='datepicker'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Invoice Number</td>";
					echo "<td><input type=text id='invoiceNumber' name='invoiceNumber' value = ''</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Family</td>";
					echo writeFamilySelector("family", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td><input type=text id='name' name='name' value = ''</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input type=text id='description' name='description' size=50 value = '' </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "I", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id='amount' name='amount' size=10 value = ''></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id='notes' name='notes' size=50 value = ''></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow up Required?</td>";
					echo "<td><input type='checkbox' name='followup' value='true'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Advised By</td>";
					echo "<td><input type=text id='advisedBy' name='advisedBy' value = ''</td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function showEditIncomeItemForm()
			{
				if ($_SESSION['newIncomeItem']->IsError())
				{
					$errors = $_SESSION['newIncomeItem']->GetErrorMessages();
					echo "<h3>Correct Income Item Details</h3>";
					echo "<p>Errors:";
					foreach ($errors as $message)
					{
						echo "<br>" . $message;
					}
				}
				else
				{
					echo "<h3>Edit Income Item</h3>";
				}
				
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Date</td>";
					echo "<td><input id='incomeDate' name='incomeDate' value = '" . $_SESSION['newIncomeItem']->GetIncomeDate()->format("d-M-Y") . "' class='datepicker'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, $_SESSION['newIncomeItem']->GetPaymentMethod());
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Invoice Number</td>";
					echo "<td><input type=text id='invoiceNumber' name='invoiceNumber' value = '" . $_SESSION['newIncomeItem']->GetInvoiceNumber() . "'</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Family</td>";
					echo writeFamilySelector("family", $_SESSION['newIncomeItem']->GetFamily());
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td><input type=text id='name' name='name' value = '" . $_SESSION['newIncomeItem']->GetName() . "'</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input type=text id='description' name='description' size=50 value = '" . $_SESSION['newIncomeItem']->GetDescription() . "' </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "I", $_SESSION['newIncomeItem']->GetCategory());
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id='amount' name='amount' size=10 value = '" . $_SESSION['newIncomeItem']->GetAmount() . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id='notes' name='notes' size=50 value = '" . $_SESSION['newIncomeItem']->GetNotes() . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					$checkedString = "";
					if ($_POST['followup'])
					{
						$checkedString = "CHECKED";
					}
					else 
					{
						$followUps = $_SESSION['newIncomeItem']->GetFollowupItems();
						if (count($followUps) > 0)
						{
							$checkedString = "CHECKED";
						}
					}
					
					echo "<td>Follow up Required?</td>";
					echo "<td><input type='checkbox' name='followup' value='true' " . $checkedString . "></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Advised By</td>";
					echo "<td><input type=text id='advisedBy' name='advisedBy' value = ''</td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function showBlankFollowupForm()
			{
				displayIncomeItemsSummary();
				
				echo "<h2>Enter Follow Up Needed</h2>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Follow Up Date</td>";
					echo "<td><input id='followDate' name='followDate' value = '" . date('d-M-Y', time()) . "' class='datepicker'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Reason for Follow Up</td>";
					echo "<td><input type=text size=50 id=\"followUpReason\" name=\"followUpReason\" value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow Up Needed</td>";
					echo "<td><input type=text size=50 id=\"followUpNeeded\" name=\"followUpNeeded\" value = \"\" </td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function showEditFollowupForm($followUp)
			{
				displayIncomeItemsSummary();
				
				if ($followUp->IsError())
				{
					$errors = $followUp->GetErrorMessages();
					echo "<h3>Correct Followup Details</h3>";
					echo "<p>Errors:";
					foreach ($errors as $message)
					{
						echo "<br>" . $message;
					}
				}
				else
				{
					echo "<h3>Edit Followup Details</h3>";
				}
				
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Follow Up Date</td>";
					echo "<td><input id='followDate' name='followDate' value = '" . $followUp->GetFollowupDate()->format("d-M-Y") . "' class='datepicker'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Reason for Follow Up</td>";
					echo "<td><input type=text size=50 id='followUpReason' name='followUpReason' value = '" . $followUp->GetFollowupReason() . "' </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow Up Needed</td>";
					echo "<td><input type=text size=50 id='followUpNeeded' name='followUpNeeded' value = '" . $followUp->GetFollowupAction() . "' </td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function displayIncomeItemsSummary()
			{
				echo "<h2>Income Item Summary</h2>";
				
				echo "<table border=1>
					<tr>
						<th>Income Date</th>
						<th>Payment Method</th>
						<th>Name</th>
						<th>Description</th>
						<th>Amount</th>
					</tr>";
				echo "<tr>"	;
					echo "<td>" . $_SESSION['newIncomeItem']->GetIncomeDate()->format("d-m-Y") . "</td>";
					echo "<td>" . getPaymentMethodDescription($_SESSION['newIncomeItem']->GetPaymentMethod()) . "</td>";
					echo "<td>" . $_SESSION['newIncomeItem']->GetName() . "</td>";
					echo "<td>" . $_SESSION['newIncomeItem']->GetDescription() . "</td>";
					echo "<td>" . money_format("%#1n", $_SESSION['newIncomeItem']->GetAmount()) . "</td>";
				echo "</tr></table>";
			}
			
			function createIncomeItemFromPost()
			{
				$incomeItem = new IncomeItem();
				$incomeItem->SetIncomeDate(date_create_from_format( "j-M-Y", $_POST['incomeDate']));
				$incomeItem->SetPaymentMethod($_POST['paymentMethod']);
				$incomeItem->SetInvoiceNumber($_POST['invoiceNumber']);
				$incomeItem->SetFamily($_POST['family']);
				$incomeItem->SetName($_POST['name']);
				$incomeItem->SetDescription($_POST['description']);
				$incomeItem->SetCategory($_POST['ledgerCategoryId']);
				$incomeItem->SetAmount($_POST['amount']);
				$incomeItem->SetNotes($_POST['notes']);
				$incomeItem->SetAdvisedBy($_POST['advisedBy']);
				
				return $incomeItem;
			}
			
			function createFollowupFromPost()
			{
				$followupItem = new FollowupItem();
				$followupItem->SetFollowupDate(date_create_from_format("j-M-Y", $_POST['followDate']));
				$followupItem->SetFollowupAction($_POST['followUpNeeded']);
				$followupItem->SetFollowupReason($_POST['followUpReason']);
				
				return $followupItem;
			}
		?>
	
		</form>
	</div>
	
	<div style="clear: both;">&nbsp;</div>
	
	</div>

	<div id="footer">
		<p>Copyright (c)2016 Artistan Solutions</p>
	</div>

	<script type="text/javascript"></script> 
</body>

</html>