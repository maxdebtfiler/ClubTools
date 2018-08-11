<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	include 'CommonIncomeExpenseFunctions.php';
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
	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Create a Receipt</TITLE>
	
	
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
					<div id="NewsDate"><a href="index.php?news&nid=1">08-05-2016</a></div>
					<div id="NewsOverview"><a href="index.php?news&nid=1">This website is still under construction, please visit us later!</a></div>
				</div>
				<div style="clear:both;"></div>
			</p>
		</div>
	</div>

	<div id="colTwo">
		<h2>Create a New Receipt</h2>
		<form action="AddReceipt.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['viewDetails'])
			{
				if (sizeof($_POST['incomeIds']) > 0)
				{
					$incomeIds = $_POST['incomeIds'];
					$_SESSION['incomeIds'] = $incomeIds;
					$_SESSION['incomeIndexDisplayed'] = 0;
					showIncomeDetails($_SESSION['incomeIndexDisplayed']);
				}
				else
				{
					displayUnreceiptedIncomeItems(null);
				}		

			}
			else if ($_POST['viewPreviousIncome'])
			{
				$_SESSION['incomeIndexDisplayed'] = $_SESSION['incomeIndexDisplayed'] - 1;
				showIncomeDetails($_SESSION['incomeIndexDisplayed']);
			}
			else if ($_POST['viewNextIncome'])
			{
				$_SESSION['incomeIndexDisplayed'] = $_SESSION['incomeIndexDisplayed'] + 1;
				showIncomeDetails($_SESSION['incomeIndexDisplayed']);
			}
			else if ($_POST['addExpenses'])
			{
				if (sizeof($_POST['incomeIds'])>0)
				{
					$_SESSION['incomeIds'] = $_POST['incomeIds'];
					displayUnpaidExpenses(null);
				}
				else
				{
					echo "<h3>Select unreceipted income transactions before selecting expenses.</h3>";
					
					displayUnreceiptedIncomeItems(null);
				
					echo "<input type=\"submit\" name=\"viewDetails\" value=\"View Details\">";
					echo "<input type=\"submit\" name=\"addExpenses\" value=\"Add Expenses\">";
					echo "<input type=\"submit\" name=\"addReceiptDetails\" value=\"Next\">";
				}
			}
			else if ($_POST['addExpensesToReceipt'])
			{
				if (sizeof($_POST['expenseIds']))
				{
					$expenseIds = $_POST['expenseIds'];
					$_SESSION['expenseIds'] = $expenseIds;	
				}
				
				displayReceiptEntryForm();
			}
			else if ($_POST['addReceiptDetails'])
			{
				if (sizeof($_POST['incomeIds'])>0)
				{
					$_SESSION['incomeIds'] = $_POST['incomeIds'];
					displayReceiptEntryForm();
				}
				else
				{
					unset($_SESSION['receiptId']);
					unset($_SESSION['incomeIds']);
					unset($_SESSION['expenseIds']);
					
					displayUnreceiptedIncomeItems(null);
					
					echo "<input type=\"submit\" name=\"viewDetails\" value=\"View Details\">";
					echo "<input type=\"submit\" name=\"addExpenses\" value=\"Add Expenses\">";
					echo "<input type=\"submit\" name=\"addReceiptDetails\" value=\"Next\">";
				}
			}
			else if ($_POST['savereceipt'])
			{
			
				// Create $receipt
				$bankDate = $_POST['bankDateYear'] . "-" . $_POST['bankDateMonth'] . "-" . $_POST['bankDateDay'];
				$date = new DateTime($bankDate);
				
				$receiptRecord = array();
				$receiptRecord['bankDate'] = $date;
				$receiptRecord['paymentMethod'] = $_POST['paymentMethod'];
				$receiptRecord['payer'] = $_POST['payer'];
				$receiptRecord['receiptNumber'] = $_POST['receiptNumber'];
				$receiptRecord['account'] = $_POST['account'];
				$receiptRecord['amount'] = $_POST['amount'];
				$receiptRecord['notes'] = $_POST['notes'];
				
				saveReceipt($receiptRecord);
			}
			else if ($_POST['returnToList'])
			{
				displayUnreceiptedIncomeItems($_SESSION['incomeIds']);
				
				echo "<input type=\"submit\" name=\"viewDetails\" value=\"View Details\">";
				echo "<input type=\"submit\" name=\"addExpenses\" value=\"Add Expenses\">";
				echo "<input type=\"submit\" name=\"addReceiptDetails\" value=\"Next\">";
			}
			else if ($_POST['updateEstimatedAmounts'])
			{
				// Update finalised amounts
				updateFinalisedAmounts($_POST['finalisedIds'], $_POST['finalisedAmount']);
				
				displayReceiptEntryForm();
			}
			else
			{
				unset($_SESSION['receiptId']);
				unset($_SESSION['incomeIds']);
				unset($_SESSION['expenseIds']);
				
				displayUnreceiptedIncomeItems(null);
				
				echo "<input type=\"submit\" name=\"viewDetails\" value=\"View Details\">";
				echo "<input type=\"submit\" name=\"addExpenses\" value=\"Add Expenses\">";
				echo "<input type=\"submit\" name=\"addReceiptDetails\" value=\"Next\">";
			}
			
			function updateFinalisedAmounts($ids, $finalisedAmounts)
			{
				for ($i=0; $i< sizeof($ids); $i++)
				{
					$sql = "UPDATE income SET amount = " . $finalisedAmounts[$i] . " WHERE id = " . $ids[$i];
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				}
			}
			
			function displayReceiptEntryForm()
			{
				$expensesTotal = 0;
				$incomeTotal = displaySelectedIncomeSummary($_SESSION['incomeIds']);
				
				if (sizeof($_SESSION['expenseIds']) >0 )
				{
					$expensesTotal = displaySelectedExpensesSummary($_SESSION['expenseIds']);
				}
				
				//Receipt entry bit
				$totalAmount = $incomeTotal - $expensesTotal;
				
				if ($totalAmount < 0)
				{
					echo "<h3>Total expenses exceeds total income. Please reselect or process as a payment.</h3>";
				}
				else
				{
					showBlankReceiptForm($totalAmount);
				}
				
			}
			
			function saveReceipt($receipt)
			{
				$error = validateReceipt($receipt);
				
				if ($error["inError"])
				{
					$success = false;
					echo "<h3>" . $error["message"] . "</h3>";
					showReceiptFormForCorrection($receipt);
					
					echo "<input type=\"submit\" name=\"savereceipt\" value=\"Save\">";
					echo "<input type=\"submit\" name=\"cancelreceipt\" value=\"Cancel\">";
				}
				else
				{
					$sql = "INSERT INTO bank_receipts (bank_date, payment_method, payee, account, amount, receipt_number, notes) VALUES (";
					$sql = $sql . "'" . $receipt['bankDate']->format('Y-m-d H:i:s') . "', " . $receipt['paymentMethod'] . ", '" . cleanString($receipt['payer']) . "', ";
					$sql = $sql . $receipt['account'] . ", " . $receipt['amount'] . ", ";
					$sql = $sql . nullIfEmpty($receipt['receiptNumber'], true) . ", " . nullIfEmpty($receipt['notes'], true) . ")";
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
					$receiptId = mysqli_insert_id($_SESSION['databaseConnection']);
				
					if ($receiptId > 0)
					{
						foreach ($_SESSION['incomeIds'] as $incomeId)
						{
							$sql = "INSERT INTO payment_transactions (income_id, receipt_id) VALUES (" . $incomeId . ", " . $receiptId . ")";
							mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
						}
						
						foreach ($_SESSION['expenseIds'] as $expenseId)
						{
							$sql = "INSERT INTO payment_transactions (expense_id, receipt_id) VALUES (" . $expenseId . ", " . $receiptId . ")";
							mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
						}
					}
					
					// Update estimated transactions to final
					$sql = "UPDATE income SET amount_estimated = false WHERE id IN (";
					
					$i = 0;
					foreach ($_SESSION['incomeIds'] as $incomeId)
					{
						$i++;
						$sql = $sql . $incomeId;
						
						if ($i < sizeof($_SESSION['incomeIds']))
						{
							$sql = $sql . ", ";
						}
					}
					
					$sql = $sql . ")";
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				}
			}
			
			function validateReceipt($receipt)
			{
				$error = array();
				$error["inError"] = false;
				
				$today = new DateTime();
				
				if ($receipt['bankDate'] > $today)
				{
					$error["inError"] = true;
					$error["errorType"] = "ERROR";
					$error["field"] = "receiptDate";
					$error["message"] = "Date recorded for the receipt cannot be in the future.";
				}
				
				return $error;
			}
			
			function getIncomeDate($incomeId)
			{
				$incomeDate = null;
				$sql = "SELECT income_date FROM income WHERE id = " . $incomeId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				$row = mysqli_fetch_array($result);
				
				$incomeDate = new DateTime($row['income_date']);
				
				return $incomeDate;
			}
			
			function getPayer($incomeId)
			{
				$name = null;
				$sql = "SELECT name FROM income WHERE id = " . $incomeId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				$row = mysqli_fetch_array($result);
				
				$name = $row['name'];
				
				return $name;
			}
			
			function showBlankReceiptForm($itemAmount)
			{
				echo "<h2>Enter Bank Receipt Details</h2>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Bank Date</td>";
					$firstIncomeDate = getIncomeDate($_SESSION['incomeIds'][0]);
					$firstIncomeDateFormatted = $firstIncomeDate->format('Y-m-d h:i:s a');
					writeDateSelector("bankDate", $firstIncomeDateFormatted);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payer</td>";
					$firstPayer = getPayer($_SESSION['incomeIds'][0]);
					echo "<td><input type=text id='payer' name='payer' value = '" . $firstPayer . "' </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Receipt Number</td>";
					echo "<td><input type=text id=\"receiptNumber\" name=\"receiptNumber\" value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Account</td>";
					writeAccountsSelector("account", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=10 value = \"" . $itemAmount . "\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id=\"notes\" name=\"notes\" size=50 value = \"\" </td>";
				echo "</tr>";
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"savereceipt\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"cancelreceipt\" value=\"Cancel\">";
			}
			
			function displaySelectedExpensesSummary($expenseIds)
			{
				$totalAmount = 0;
				
				echo "<h3>SUnpaid expenses included in receipt</h3>";
				
				$sql = "SELECT id, payee, payment_due_date, invoice_number, description, amount, ledger_category FROM expenses WHERE id IN (";
				
				$i = 0;
				foreach ($expenseIds as $id)
				{
					$sql = $sql . $id;
					
					$i++;
					if ($i < sizeof($expenseIds))
					{
						$sql = $sql . ", ";
					}
				}
				
				$sql = $sql . ")";
				
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				
				echo "<table border=1>
					<tr>
						<th>Due Date</th>
						<th>Payee</th>
						<th>Invoice Number</th>
						<th>Descritpion</th>
						<th>Category</th>
						<th>Amount</th>
					</tr>";
				
				while ($row = mysqli_fetch_array($result))
				{
					echo "<tr>";
						echo "<td>" . $row['income_date'] . "</td>" .
							"<td>" . $row['payee'] . "</td>" .
							"<td>" . $row['invoice_number'] . "</td>" .
							"<td>" . $row['description'] . "</td>" .
							"<td>" . getCategoryDescription($row['ledger_category']) . "</td>" .
							"<td>" . $row['amount'] . "</td>";
					echo "</tr>";
					
					$totalAmount = $totalAmount + $row['amount'];
				}
				
				echo "</table>";
				
				return $totalAmount;
			}
			
			function showIncomeDetails($index)
			{
				$incomeId = $_SESSION['incomeIds'][$index];
				$sql = "SELECT income_date, invoice_number, family_code, name, description, amount, payment_method, ledger_category, amount_estimated, notes, advised_by, followup_status FROM income WHERE id = " . $incomeId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				$row = mysqli_fetch_array($result);
				
				$incomeDate = new DateTime($row['income_date']);
				
				echo "<table border=1>";
				
				echo "<tr><td>ID</td><td>" . $incomeId . "</td></tr>";
				echo "<tr><td>Income Date</td><td>" . $incomeDate->format('d-M-Y') . "</td></tr>";
				echo "<tr><td>Invoice Number</td><td>" . $row['invoice_number'] . "</td></tr>";
				echo "<tr><td>Family Code</td><td>" . $row['family_code'] . "</td></tr>";
				echo "<tr><td>Name</td><td>" . $row['name'] . "</td></tr>";
				echo "<tr><td>Description</td><td>" . $row['description'] . "</td></tr>";
				echo "<tr><td>Payment Method</td><td>" . getPaymentMethodDescription($row['payment_method']) . "</td></tr>";
				echo "<tr><td>Ledger Category</td><td>" . getCategoryDescription($row['ledger_category']) . "</td></tr>";
				echo "<tr><td>Amount</td><td>" . $row['amount'] . "</td></tr>";
				echo "<tr><td>Notes</td><td>" . $row['notes'] . "</td></tr>";
				echo "<tr><td>Advised By</td><td>" . $row['advised_by'] . "</td></tr>";
				echo "<tr><td>Follow Up Status</td><td>" . $row['followup_status'] . "</td></tr>";
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"returnToList\" value=\"Return to List\">";
				
				if (sizeof($_SESSION['incomeIds']) > 1 && $index > 0)
				{
					echo "<input type=\"submit\" name=\"viewPreviousIncome\" value=\"View Previous\">";
				}
				
				if (sizeof($_SESSION['incomeIds']) > 1 && $index < (sizeof($_SESSION['incomeIds'])-1))
				{
					echo "<input type=\"submit\" name=\"viewNextIncome\" value=\"View Next\">";
				}
			}	
			
			function displaySelectedIncomeSummary($selectedIncomeIds)
			{
				$totalAmount = 0;
				
				echo "<h3>Unreceipted income items included in receipt</h3>";
				
				$sql = "SELECT id, income_date, name, description, amount, payment_method, ledger_category, amount_estimated, notes FROM income WHERE id IN (";
				
				$i = 0;
				foreach ($selectedIncomeIds as $id)
				{
					$sql = $sql . $id;
					
					$i++;
					if ($i < sizeof($selectedIncomeIds))
					{
						$sql = $sql . ", ";
					}
				}
				
				$sql = $sql . ")";
				
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				$hasEstimatedAmount = isEstimatedAmountInGroup($result);
				mysqli_data_seek($result, 0);
				
				echo "<table border=1>
					<tr>
						<th>Date</th>
						<th>Name</th>
						<th>Description</th>
						<th>Category</th>
						<th>Amount</th>";
				
				if ($hasEstimatedAmount)
				{
					echo "<th>Finalised Amount</th>";
				}
				
				echo "</tr>";
				
				while ($row = mysqli_fetch_array($result))
				{
					$displayDate = date_create_from_format("Y-m-d", $row['income_date']);
					echo "<tr>";
					echo "<td>" . $displayDate->format("d-m-Y") . "</td>" .
						"<td>" . $row['name'] . "</td>" .
						"<td>" . $row['description'] . "</td>" .
						"<td>" . getCategoryDescription($row['ledger_category']) . "</td>" .
						"<td>" . money_format("%#1n", $row['amount']) . "</td>";
					
					if ($row['amount_estimated'])
					{
						echo "<td><input type=text id=\"finalisedAmount\" name=\"finalisedAmount[]\" size=10 value = \"" . $row['amount'] . "\">";
						echo "<input type=hidden id=\"incomeIdFinalise\" name=\"finalisedIds[]\" value=\"" . $row['id'] . "\"></td>";
					}
					echo "</tr>";
					
					$totalAmount = $totalAmount + $row['amount'];
				}
				
				echo "</table>";
				
				if ($hasEstimatedAmount)
				{
					echo "<input type=\"submit\" name=\"updateEstimatedAmounts\" value=\"Update\">";
				}
				
				return $totalAmount;
			}
			
			function isEstimatedAmountInGroup($result)
			{
				$hasEstimatedAmount = false;
				
				while (!$hasEstimatedAmount && $row = mysqli_fetch_array($result))
				{
					$hasEstimatedAmount = $row['amount_estimated'];
				}
				
				
				
				return $hasEstimatedAmount;
			}
			
			function displayUnpaidExpenses($selectedExpenseIds)
			{
				echo "<h3>Select unpaid expenses</h3>";
				
				$sql = "SELECT id, created, payee, invoice_number, description, payment_due_date, ledger_category, amount, notes FROM expenses WHERE id NOT IN " .
					"(SELECT expense_id FROM payment_transactions WHERE expense_id IS NOT NULL)";
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				
				echo "<table border=1>
					<tr>
						<th/>
						<th>Payee</th>
						<th>Description</th>
						<th>Category</th>
						<th>Approval Status</th>
						<th>Amount</th>
					</tr>";
				
				$i=1;
				while ($row = mysqli_fetch_array($result))
				{
					$approvalStatus = getApprovalStatus($row['id']);
					
					echo "<tr>" .
						"<td><input type=\"checkbox\" name=\"expenseIds[]\" id=\"" . $row['id'] . "\" value=\"" . $row['id'] . "\"" . isCheckboxChecked($row['id'], $selectedExpenseIds) . "></td>" .
						"<td>" . colourTextIfApproved($row['payee'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($row['description'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved(getCategoryDescription($row['ledger_category']), $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($approvalStatus, $approvalStatus) . "</td>" .
						"<td>" . (colourTextIfApproved(money_format("%#1n", $row['amount']), $approvalStatus)) . "</td>" .
						"</tr>";
					$i++;
				}
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"returnToList\" value=\"Continue Without Expenses\">";
				echo "<input type=\"submit\" name=\"addExpensesToReceipt\" value=\"Include Expenses\">";
			}
			
			function colourTextIfApproved($text, $status)
			{
				$response = $text;
				if ($status == "Fully Approved")
				{
					$response = "<font color=\"green\">" . $text . "</font>";
				}
				
				return $response;
			}
			
			function getApprovalStatus($expenseId)
			{
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
					$status = "Fully Approved";
				}
				
				return $status;
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