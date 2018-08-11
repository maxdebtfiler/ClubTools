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
		<form action="ApproveExpenseFromEmail.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ( $_GET['mode'] != null)
			{
				$mode = $_GET['mode'];
				
				if ($mode == "fast" || $mode == "reviewinvoice")
				{
					$_SESSION["approvalId"] = $_GET['id'];
				}
				
				
			}
			else if ($_POST['reviewinvoice'])
			{
				$mode = "invoice";
			}
			else if ($_POST['invoiceapproved'])
			{
				$mode = "invoiceApproved";
			}
			else if ($_POST['invoicerejected'])
			{
				$mode = "invoiceRejected";
			}
			
			
			switch ($mode)
			{
				case "fast":
					$expenseId = getExpenseFromApprovalId($_SESSION["approvalId"]);
					recordApproval($_SESSION["approvalId"], null);
					echo "<h3>Your Approval Has Been Recorded</h3>";
					showInvoice($expenseId);
					emailNotificationIfFullyApproved($expenseId);
					//echo "<input type=\"submit\" name=\"viewinvoice\" value=\"Download Invoice\">";
					//echo "<input type=\"submit\" name=\"reviewinvoice\" value=\"Oops Changed My Mind\">";
					//echo "<input type=\"submit\" name=\"reviewmyapprovals\" value=\"Review My Outstanding Approvals\">";
					break;
				case "reviewinvoice":
					reviewInvoice();
					break;
				case "approver":
					break;
				case "invoiceApproved":
					echo "<h3>Your Approval Has Been Recorded</h3>";
					
					// Check if a comment has been provided and save
					recordApproval($_SESSION["approvalId"], $_POST['approvalComment']);
					showInvoice(getExpenseFromApprovalId($_SESSION['approvalId']));
					echo "<input type=\"submit\" name=\"viewinvoice\" value=\"Download Invoice\">";
					echo "<input type=\"submit\" name=\"reviewinvoice\" value=\"Oops Changed My Mind\">";
					echo "<input type=\"submit\" name=\"reviewmyapprovals\" value=\"Review My Outstanding Approvals\">";
					break;
				case "invoiceRejected":
					if (validate($_POST['approvalComment']))
					{
						$error = recordRejection($_SESSION['approvalId'], $_POST['approvalComment']);
						
					}
					else
					{
						echo "<h3>Validation Error: Comment required when rejecting</h3>";
						reviewInvoice();
					}
			}
			
			function emailNotificationIfFullyApproved($expenseId)
			{
				if (isFullyApproved($expenseId) && !isNotified($expenseId))
				{
					// Add the attachment
					$sql = "SELECT payee, payment_due_date, description, ledger_category, invoice_number, amount, image_type, image_length, image, image_name, invoice_filename from expenses where id = " . $expenseId;
					$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
					
					$row = mysqli_fetch_array($result);
					$payee = $row['payee'];
					$date = new DateTime($row['payment_due_date']);
					$dueDate = $date->format("d-M-y");
					$description = $row['description'];
					$invoiceNumber = $row['invoice_number'];
					$category = $row['ledger_category'];
					$amount = $row['amount'];
					
					$size = $row['image_length'];
					$type = $row['image_type'];
					$imageName = $row['image_name'];
					$image = $row['image'];
					$fileName = $row['invoice_filename'];
					
					$notifyList = getNotifyList();
					
					foreach($notifyList as $emailAddress => $name)
					{
						$mail = new PHPMailer();
				
						$mail->IsSMTP();
						$mail->Host = $_SESSION['preferences']['mail_host'];
						$mail->SetFrom = $_SESSION['preferences']['mail_from'];
						$mail->From = $_SESSION['preferences']['mail_from'];
						$mail->FromName = $_SESSION['preferences']['mail_from_display_name'];
				
						$mail->AddAddress($emailAddress);
					
						$mail->Subject = "EXPENSE FULLY APPROVED " . $payee . " Due " . $dueDate;
						$mail->IsHTML(true);
						$mail->Body = createNotificationEmailBody($expenseId, $name, $payee, $invoiceNumber, $category, $description, $dueDate, $amount);
						$mail->WordWrap = 50;
					
						if ($fileName != null)
						{
							$mail->AddAttachment($fileName);
						}
						else if ($image != null)
						{
							$mail->AddStringAttachment($image, $imageName, "base64", $type, "attachment");
						}
						
						if ($mail->Send())
						{
							$sql = "UPDATE expenses SET approval_notification = 1 WHERE id = " . $expenseId;
							mysqli_query($_SESSION['databaseConnection'], $sql);
						}
						else
						{
							echo "Send mail failed.";
							echo "Error: " . $mail->ErrorInfo;
						}
					}
				}
			}
			
			function createNotificationEmailBody($expenseId, $name, $payee, $invoiceNumber, $category, $description, $dueDate, $amount)
			{
				$body = "<p style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\">Dear " . $name . ",</p>" .
					"<p style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\">The following payment is fully approved and ready for payment. " .
					"Please login to create the payment record.</p>" .
					"<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px; width: 500px;\">" .
					"<tbody>" .
						"<tr>" .
							"<td>Payee</td>" .
							"<td>" . $payee . "</td>" .
						"</tr>" .
						"<tr>" .
							"<td>Invoice Number</td>" .
							"<td>" . replaceIfNull($invoiceNumber, "None") . "</td>" .
						"</tr>" .
						"<tr>" .
							"<td>Description</td>" .
							"<td>" . replaceIfNull($description, "None") . "</td>" .
						"</tr>" .
						"<tr>" .
							"<td>Category</td>" .
							"<td>" . getCategoryDescription($category) . "</td>" .
						"</tr>" .
						"<tr>" .
							"<td>Due Date</td>" .
							"<td>" . $dueDate . "</td>" .
						"</tr>" .
						"<tr>" .
							"<td><span style=\"color:#0000CD;\"><strong>Amount</strong></span></td>" .
							"<td><span style=\"color:#0000CD;\"><strong>" . money_format("%#1n", $amount) . "</strong></span></td>" .
						"</tr>" .
					"</tbody>" .
					"</table>";
					
				return $body;
			}
			
			function getNotifyList()
			{
				$recipients = array();
				$sql = "SELECT given_names, email FROM approvers WHERE notify_expense_fully_approved = true";
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				
				while ($row = mysqli_fetch_array($result))
				{
					$theEmail = $row['email'];
							
					if (count($recipients) > 0)
					{
						if (!$array_key_exists($theEmail, $recipients))
						{
							$recipients[$theEmail] = $row['given_names'];
						}
					}
					else
					{
						$recipients[$theEmail] = $row['given_names'];
					}
				}
				
				return $recipients;
			}
			
			function isNotified($expenseId)
			{
				$notified = false;
				
				$sql = "SELECT approval_notification FROM expenses where id = " . $expenseId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row['approval_notification'] == 1)
				{
					$notified = true;
				}
				
				return $notified;
			}
			
			function isFullyApproved($expenseId)
			{
				$approved = false;
				$status = "Pending";
				$numApprovals = 0;
				
				$sql = "SELECT approver_id, approved, denied FROM approvals WHERE expense_id = " . $expenseId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				$approvalsRequested = mysqli_num_rows($result);
				
				while ($status != "Declined" && $row = mysqli_fetch_array($result))
				{
					if ($row['declined'] != null)
					{
						$status = "Declined";
					}
					else if ($row['approved'] != null)
					{
						$numApprovals++;
					}
				}
				
				if ($status == "Pending" && $numApprovals == $approvalsRequested)
				{
					$approved = true;
				}
				
				return $approved;
			}
			
			function validate($comment)
			{
				$response = true;
				if ($comment == null)
				{
					$response = false;
				}
				
				return $response;
			}
			
			function reviewInvoice()
			{
				showInvoice(getExpenseFromApprovalId($_SESSION["approvalId"]));
				echo "<p><h4>Your Response</h4>";
				displayResponsePrompt();
			}
			function displayResponsePrompt()
			{
				echo "<table>";
				echo "<tr><td>Comment</td><td><input type=\"text\" name=\"approvalComment\" size=\"75\" value=\"\"</td></tr>";
				echo "</table>";
				echo "<p>";
				echo "<input type=\"submit\" name=\"invoiceapproved\" value=\"Approve\">";
				echo "<input type=\"submit\" name=\"invoicerejected\" value=\"Reject\">";
			}
			
			function showInvoice($expenseId)
			{
				$sql = "SELECT created, payee, invoice_number, description, payment_due_date, ledger_category, amount, notes, image_length FROM expenses WHERE id = " . $expenseId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed');
				$row = mysqli_fetch_array($result);
				
				echo "<table border=1>";
				echo "<tr><td>ID</td><td>" . $expenseId . "</td></tr>";
				echo "<tr><td>Payee</td><td>" . $row['payee'] . "</td></tr>";
				echo "<tr><td>Invoice Number</td><td>" . replaceIfNull($row['invoice_number'], "None") . "</td></tr>";
				echo "<tr><td>Description</td><td>" . $row['description'] . "</td></tr>";
				echo "<tr><td>Payment Due</td><td>" . colourIfOverdue($row['payment_due_date']) . "</td></tr>";
				echo "<tr><td>Category</td><td>" . getCategoryDescription($row['ledger_category']) . "</td></tr>";
				echo "<tr><td>Amount</td><td>" . money_format("%#1n", $row['amount']) . "</td></tr>";
				echo "<tr><td>Notes</td><td>" . $row['notes'] . "</td></tr>";
				echo "</table>"; 
				
				$sql = "SELECT ap.approver_id, ap.requested, ap.approved, ap.denied, ap.comment, a.surname, a.given_names FROM approvals ap, approvers a WHERE a.id = ap.approver_id AND expense_id = " . $expenseId;
				$approvalsResult = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed');
				
				echo "<h4>Approval Status: " . getApprovalStatus($approvalsResult) . "</h4>";
				$approvalsResult->data_seek(0);
				
				echo "<table border=1>";
				
				while ($row = mysqli_fetch_array($approvalsResult))
				{
					$approverId = $row['approver_id'];
					echo "<tr>";
					echo "<td>" . $row['given_names'] . " " . $row['surname'] . "</td>";
					echo "<td>" . getApproverApprovalStatus($row) . "</td>";
					echo "<td>" . replaceIfNull($row['comment'], "No comment") . "</td>";
					echo "</tr>";
				}
				
				echo "</table>";
				echo "<input type=\"hidden\" name=\"approverId\" value=\"" . $approverId . "\"";
				echo "<input type=\"hidden\" name=\"expenseId\" value=\"" . $expenseId . "\"";
				
			}
			function getApproverApprovalStatus($row)
			{
				$status = "Pending";
				
				if ($row['approved'] != null)
				{
					$status = "Approved";
				}
				else if ($row['declined'] != null)
				{
					$status = "Declined";
				}
				
				return $status;
			}
			
			function getApprovalStatus($approvalsResult)
			{
				$status = "Pending";
				$numApprovals = 0;
				
				$approvalsRequested = mysqli_num_rows($approvalsResult);
				
				while ($status != "Declined" && $row = mysqli_fetch_array($approvalsResult))
				{
					if ($row['declined'] != null)
					{
						$status = "Declined";
					}
					else if ($row['approved'] != null)
					{
						$numApprovals++;
					}
				}
				
				if ($status == "Pending" && $numApprovals == $approvalsRequested)
				{
					$status = "Fully Approved";
				}
				
				return $status;
			}
			
			function getExpenseFromApprovalId($approvalId)
			{
				$sql = "SELECT expense_id FROM approvals WHERE id = " . $approvalId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				$row = mysqli_fetch_array($result);
				
				return $row['expense_id'];
			}
			
			function recordRejection($id, $comment)
			{
				// Check to see if we already have a response
				$error = alreadyResponded($id);
				if (!$error['inError'])
				{
					$sql = "UPDATE approvals SET denied = now() ";
				
					if ($comment != null)
					{
						$sql = $sql . ", comment = '" . $comment . "' ";
					}
					
					$sql = $sql . "WHERE id = " . $id;
					mysqli_query($_SESSION['databaseConnection'], $sql);
				}
				else
				{
					echo "<h2>" . $error['message'] . "</h2>";
					echo "<p>" . $error['remedy'] . "</p>";
				}
			}
			
			function recordApproval($id, $comment)
			{
				$error = alreadyResponded($id);
				if (!$error['inError'])
				{
					$sql = "UPDATE approvals SET approved = now() ";
					
					if ($comment != null)
					{
						$sql = $sql . ", comment = '" . $comment . "' ";
					}
					
					$sql = $sql . "WHERE id = " . $id;
					mysqli_query($_SESSION['databaseConnection'], $sql);
				}
				else
				{
					echo "<h2>" . $error['message'] . "</h2>";
					echo "<p>" . $error['remedy'] . "</p>";
				}
			}
			
			function alreadyResponded($id)
			{
				$error = null;
				$sql = "SELECT COUNT(*) as responses FROM approvals WHERE id = " . $id . 
					" AND (approved is not null OR denied is not null)";
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row['responses'] > 0)
				{
					$error = array("inError" => true, "message" => "Response already received for this approval.", "remedy" => "Please update previous response." );
				}
				
				return $error;
			}
			
			function colourIfOverdue($inputDate)
			{
				$response = date("j F Y", strtotime($inputDate));
				$today = date("d-M-Y", strtotime("now"));
				
				if ($response < $today)
				{
					$response = "<p><span style=\"color: #ff0000;\">" . $response . "</span></p>";
				}
				
				return $response;
			}
			
			
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