<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	include 'CommonIncomeExpenseFunctions.php';
	session_start();
	
	/* PHPMailer library inclusions */
 	
	
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
	<TITLE>Create a Payment</TITLE>
	
	
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
				<li><a href="AddExpense.php">Add Expense</a></li>
				<li><a href="AddPayment.php">Add Payment</a></li>
				<li><a href="UpdateExpense.php">Update an Expense</a></li>
				<li><a href="UpdatePayment.php">Update Payment</a></li>
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
		<h2>Create a Payment</h2>
		<form action="AddPayment.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['create_payment'])
			{
				if (!isset($_SESSION['expenseIds']))
				{
					$expenseIds = $_POST['expenseId'];
					$_SESSION['expenseIds'] = $expenseIds;
				}
				
				//Check that something was selected
				if (count($_SESSION['expenseIds'])==0)
				{
					$error['error'] = true;
					$error['message'] = "Please select an unpaid expense before continuing.";
				}
				
				foreach ($_SESSION['expenseIds'] as $expenseId)
				{
					$error = validateExpenseApprovalStatus($expenseId);
				}
				
				if ($error['error'])
				{
					echo "<p>" . $error['message'] . "</p>";
					echo "<input type=\"submit\" name=\"cancel_create_payment\" value=\"Ok\">";
				}
				else if ($error['warning'])
				{
					echo "<p>" . $error['message'] . "</p>";
					echo "<input type=\"submit\" name=\"continue_with_create_payment\" value=\"Continue\">";
					echo "<input type=\"submit\" name=\"cancel_create_payment\" value=\"Cancel\">";
				}
				else
				{
					createPayment();
				}
			}
			else if ($_POST['show_details'])
			{
				if (count($_POST['expenseId']) > 0)
				{
					$expenseIds = $_POST['expenseId'];
					$_SESSION['expenseIds'] = $expenseIds;
					$_SESSION['expenseIndexDisplayed'] = 0;
					showExpense($_SESSION['expenseIndexDisplayed']);
				}
				else
				{
					displayUnpaidExpenses(null);
				}					
			}
			else if ($_POST['view_next_expense'])
			{
				$_SESSION['expenseIndexDisplayed'] = $_SESSION['expenseIndexDisplayed'] + 1;
				showExpense($_SESSION['expenseIndexDisplayed']);
			}
			else if ($_POST['view_previous_expense'])
			{
				$_SESSION['expenseIndexDisplayed'] = $_SESSION['expenseIndexDisplayed'] - 1;
				showExpense($_SESSION['expenseIndexDisplayed']);
			}
			else if ($_POST['continue_with_create_payment'])
			{
				$expenseId = $_POST['expenseId'];
				createPayment();
			}
			else if ($_POST['cancel_create_payment'])
			{
				unset($_SESSION['expenseIds']);
				displayUnpaidExpenses(null);
			}
			else if ($_POST['return_to_list'])
			{
				displayUnpaidExpenses($_SESSION['expenseIds']);
			}
			else if ($_POST['create_payment_payment_details'])
			{
				$paymentDate = $_POST['createdDateYear'] . "-" . $_POST['createdDateMonth'] . "-" . $_POST['createdDateDay'];
				createPaymentWithDetails($paymentDate, $_POST['payee'], $_POST['paymentMethod'], $_POST['account'], $_POST['amount']);
			}
			else if ($_POST['save'])
			{
				$error = validateExpense();
				save($_POST['paymentDate'], $_POST['payee'], $_POST['paymentMethod'], $_POST['account'], $_POST['amount'], $_POST['chequeNumber'], $_POST['bsb'], $_POST['bsbAccountNumber'], $_POST['bsbAccountName'], $_POST['bsbReference'], $_POST['bpayBillerCode'], $_POST['bpayReferenceNumber']);
			}
			else if ($_POST['add_more_expenses'])
			{
				displayUnpaidExpenses($_SESSION['expenseIds']);
			}
			else if ($_POST['add_income'])
			{
				addIncome();
			}
			else if ($_POST['view_income_details'])
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
			else if ($_POST['return_to_income_list'])
			{
				addIncome();
			}
			else if ($_POST['view_previous_income'])
			{
				$_SESSION['incomeIndexDisplayed'] = $_SESSION['incomeIndexDisplayed'] - 1;
				showIncomeDetails($_SESSION['incomeIndexDisplayed']);
			}
			else if ($_POST['view_next_income'])
			{
				$_SESSION['incomeIndexDisplayed'] = $_SESSION['incomeIndexDisplayed'] + 1;
				showIncomeDetails($_SESSION['incomeIndexDisplayed']);
			}
			else if ($_POST['continue_without_income'])
			{
				unset($_SESSION['incomeIds']);
				createPayment();
			}
			else if ($_POST['continue_with_income'])
			{
				if (sizeof($_POST['incomeIds']) > 0)
				{
					$_SESSION['incomeIds'] = $_POST['incomeIds'];
				}
				createPayment();
			}
			else
			{
				unset($_SESSION['expenseIds']);
				unset($_SESSION['incomeIds']);
				
				displayUnpaidExpenses(null);
			}
			
			function addIncome()
			{
				displaySummaryExpense(getExpenses($_SESSION['expenseIds']));
				displayUnreceiptedIncomeItems($_SESSION['incomeIds']);
				echo "<input type=\"submit\" name=\"view_income_details\" value=\"View Details\">";
				echo "<input type=\"submit\" name=\"continue_without_income\" value=\"Continue Without Income\">";
				echo "<input type=\"submit\" name=\"continue_with_income\" value=\"Continue With Income\">";
			}
			function validateExpense()
			{
				return true;
			}
			
			function removeSpacesAndDashes($string)
			{
				$string = str_replace(" ", "", $string);
				
				return str_replace("-", "", $string);
			}
			
			function save($paymentDate, $payee, $paymentMethod, $account, $amount, $chequeNumber, $bsb, $bsbAccountNumber, $bsbAccountName, $bsbReference, $bpayBillerCode, $bpayReferenceNumber)
			{
				// Save the payment
				$bsb = removeSpacesAndDashes($bsb);
				if (validateExpense())
				{
					$sql = "INSERT INTO payments (payee, payment_date, payment_method, account, amount, cheque_number, bsb, bsb_account_number, bsb_account_name, bpay_number, bpay_reference, receipt_number, posted_date)
						VALUES (" .
							"'" . cleanString($payee) . "', " .
							"'" . $paymentDate . "', " .
							$paymentMethod . ", " .
							$account . ", " .
							$amount . ", " .
							nullIfEmpty($chequeNumber, false) . ", " .
							nullIfEmpty($bsb, false) . ", " .
							nullIfEmpty($bsbAccountNumber, true) . ", " .
							nullIfEmpty(cleanString($bsbAccountName), true) . ", " .
							nullIfEmpty($bpayBillerCode, false) . ", " .
							nullIfEmpty($bsbReference, true) . ", " .
							"null, null" .
							")";
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
					$paymentId = mysqli_insert_id($_SESSION['databaseConnection']);
				}
				 
				// Save the payment expense records
				foreach ($_SESSION['expenseIds'] as $expenseId)
				{
					$sql = "INSERT INTO payment_transactions (payment_id, expense_id) VALUES (" . $paymentId . ", " . $expenseId . ")";
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);	
				}
				
				unset($_SESSION['expenseIds']);
				
				// Save the payment income records - will be used for multi payments that have net expense but some income components TODO
				if (isset($_SESSION['incomeIds']))
				{
					foreach ($_SESSION['incomeIds'] as $incomeId)
					{
						$sql = "INSERT INTO payment_transactions (payment_id, income_id) VALUES (" . $paymentId . ", " . $incomeId . ")";
						mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
					}
					
					unset($_SESSION['incomeIds']);
				}				
			}
			
			function createPaymentWithDetails($paymentDate, $payee, $paymentMethod, $account, $amount)
			{
				echo "<h3>Create payment record</h3>";
				$result = getExpenses($_SESSION['expenseIds']);
				displaySummaryExpense($result);
				
				echo "<h3>Enter payment details</h3>";
				echo "<table border=1>";
				
				echo "<tr>";
					$date = new DateTime($paymentDate);
					echo "<td>Payment Date</td>";
					echo "<td>" . $date->format("d-M-y") . "<input type=\"hidden\" id=\"paymentDate\" name=\"paymentDate\" value=\"" . $paymentDate . "\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payee</td>";
					echo "<td>" . $payee . "<input type=\"hidden\" id=\"payee\" name=\"payee\" value=\"" . $payee . "\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					echo "<td>" . getPaymentMethodDescription($paymentMethod) . "<input type=\"hidden\" id=\"paymentMethod\" name=\"paymentMethod\" value=\"" . $paymentMethod . "\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Account</td>";
					echo "<td>" . getAccountDescription($account) . "<input type=\"hidden\" id=\"account\" name=\"account\" value=\"" . $account . "\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td>" . money_format("%#1n", $amount) . "<input type=\"hidden\" id=\"amount\" name=\"amount\" value=\"" . $amount . "\"></td>";
				echo "</tr>";
				
				// Add rows for the payment type
				switch ($paymentMethod)
				{
					case 2: // Cheque
						echo "<tr>";
						echo "<td>Cheque Number</td>";
						echo "<td><input type=text id=\"chequeNumber\" name=\"chequeNumber\" value = \"\" </td>";
						echo "</tr>";
						break;
					case 4: // Direct Debit
						echo "<tr>";
						echo "<td>BSB</td>";
						echo "<td><input type=text id=\"bsb\" name=\"bsb\" value = \"\" </td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Account Number</td>";
						echo "<td><input type=text id=\"bsbAccountNumber\" name=\"bsbAccountNumber\" value = \"\" </td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Account Name</td>";
						echo "<td><input type=text id=\"bsbAccountName\" name=\"bsbAccountName\" value = \"\" </td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Reference</td>";
						echo "<td><input type=text id=\"bsbReference\" name=\"bsbReference\" value = \"\" </td>";
						echo "</tr>";
						break;
					case 6: // BPay
						echo "<tr>";
						echo "<td>BPay Biller Code</td>";
						echo "<td><input type=text id=\"bpayBillerCode\" name=\"bpayBillerCode\" value = \"\" </td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>Account Number</td>";
						echo "<td><input type=text id=\"bpayReferenceNumber\" name=\"bpayReferenceNumber\" value = \"\" </td>";
						echo "</tr>";
						break;
					default:
						break;
				}
				
				echo "</table>";
				echo "<input type=\"submit\" name=\"cancel_create_payment\" value=\"Cancel\">";
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
			}
			
			function showExpense($expenseIndex)
			{
				$expenseId = $_SESSION['expenseIds'][$expenseIndex];
				$sql = "SELECT created, payee, invoice_number, description, payment_due_date, ledger_category, amount, notes, image_length FROM expenses WHERE id = " . $expenseId;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				$row = mysqli_fetch_array($result);
				
				$paymentDueDate = new DateTime($row['payment_due_date']);
				
				echo "<table border=1>";
				echo "<tr><td>ID</td><td>" . $expenseId . "</td></tr>";
				echo "<tr><td>Payee</td><td>" . $row['payee'] . "</td></tr>";
				echo "<tr><td>Invoice Number</td><td>" . replaceIfNull($row['invoice_number'], "None") . "</td></tr>";
				echo "<tr><td>Description</td><td>" . $row['description'] . "</td></tr>";
				echo "<tr><td>Payment Due</td><td>" . $paymentDueDate->format('d-M-Y') . "</td></tr>";
				echo "<tr><td>Category</td><td>" . getCategoryDescription($row['ledger_category']) . "</td></tr>";
				echo "<tr><td>Amount</td><td>" . money_format("%#1n", $row['amount']) . "</td></tr>";
				echo "<tr><td>Notes</td><td>" . $row['notes'] . "</td></tr>";
				echo "</table>"; 
				
				$sql = "SELECT ap.approver_id, ap.requested, ap.approved, ap.denied, ap.comment, a.surname, a.given_names FROM approvals ap, approvers a WHERE a.id = ap.approver_id AND expense_id = " . $expenseId;
				$approvalsResult = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed');
				
				echo "<h4>Approval Status: " . getApprovalStatus($expenseId) . "</h4>";
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
				echo "<p>";
				echo "<input type=\"hidden\" name=\"expenseId\" value=\"" . $expenseId . "\"";
				echo "<p>";
				echo "<input type=\"submit\" name=\"return_to_list\" value=\"Return to List\">";
				echo "<input type=\"submit\" name=\"create_payment\" value=\"Create Payment\">";
				
				if (sizeof($_SESSION['expenseIds']) > 1 && $expenseIndex > 0)
				{
					echo "<input type=\"submit\" name=\"view_previous_expense\" value=\"View Previous\">";
				}
				
				if (sizeof($_SESSION['expenseIds']) > 1 && $expenseIndex < (sizeof($_SESSION['expenseIds'])-1))
				{
					echo "<input type=\"submit\" name=\"view_next_expense\" value=\"View Next\">";
				}
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
			
			function displaySelectedIncomeSummary($incomeIds)
			{
				$totalAmount = 0;
				
				$sql = "SELECT id, income_date, name, description, amount, payment_method, ledger_category, amount_estimated, notes FROM income WHERE id IN (";
				
				$i = 0;
				foreach ($incomeIds as $id)
				{
					$sql = $sql . $id;
					
					$i++;
					if ($i < sizeof($incomeIds))
					{
						$sql = $sql . ", ";
					}
				}
				
				$sql = $sql . ")";
				
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				
				echo "<table border=1>
					<tr>
						<th>Date</th>
						<th>Name</th>
						<th>Description</th>
						<th>Category</th>
						<th>Amount</th>
					</tr>";
				
				while ($row = mysqli_fetch_array($result))
				{
					echo "<tr>";
					echo "<td>" . $row['income_date'] . "</td>" .
						"<td>" . $row['name'] . "</td>" .
						"<td>" . $row['description'] . "</td>" .
						"<td>" . getCategoryDescription($row['ledger_category']) . "</td>" .
						"<td>" . $row['amount'] . "</td>";
					echo "</tr>";
					
					$totalAmount = $totalAmount + $row['amount'];
				}
				
				echo "</table>";
				
				return $totalAmount;
			}
			
			function createPayment()
			{
				$incomeOffset = 0;
				
				echo "<h3>Create payment record</h3>";
				$result = getExpenses($_SESSION['expenseIds']);
				displaySummaryExpense($result);
				
				echo "<input type=\"submit\" name=\"add_more_expenses\" value=\"Change Expenses\">";
				echo "<input type=\"submit\" name=\"add_income\" value=\"Add Income Record to Net\">";
				
				if (count($_SESSION['incomeIds']) > 0)
				{
					echo "<h3>Included Income Items</h3>";
					$incomeOffset = displaySelectedIncomeSummary($_SESSION['incomeIds']);
				}
				
				echo "<h3>Enter payment details</h3>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Payment Date</td>";
					echo writeDateSelector("createdDate", date('m/d/Y h:i:s a', time()));
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payee</td>";
					echo "<td> <input type=text id=\"payee\" name=\"payee\" value = \"" . findPayee($result) . "\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "O", false, null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Account</td>";
					writeAccountsSelector("account", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					$finalAmount = findAmount($result) - $incomeOffset;
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=20 value = \"" . $finalAmount . "\" </td>";
				echo "</tr>";
				echo "</table>";
				
				echo "<p>";
				
				echo "<input type=\"submit\" name=\"cancel_create_payment\" value=\"Cancel\">";
				echo "<input type=\"submit\" name=\"create_payment_payment_details\" value=\"Next\">";
			}
			
			function findPayee($result)
			{
				$payee = null;
				$payees = array();
				mysqli_data_seek ($result , 0);
				
				while ($row = mysqli_fetch_array($result))
				{
					$thisPayee = $row['payee'];
					if (array_key_exists($thisPayee, $payees))
					{
						$numInstances = $payees[$thisPayee];
						$numInstances++;
						$payees[$thisPayee] = $numInstances;
					}
					else
					{
						$payees[$thisPayee] = 1;
					}
				}
				
				$maxInstances = 0;
				foreach ($payees as $name => $count)
				{
					if ($count > $maxInstances)
					{
						$maxInstances = $count;
						$payee = $name;
					}
				}
				
				return $payee;
			}
			
			function findAmount($result)
			{
				$amount = 0;
				mysqli_data_seek ($result , 0);
				
				while ($row = mysqli_fetch_array($result))
				{
					$amount = $amount + $row['amount'];
				}
				
				return $amount;
			}
			
			function getExpenses($expenseIds)
			{
				$sql = "SELECT id, created, payee, invoice_number, description, payment_due_date, ledger_category, amount, notes FROM expenses WHERE id IN (";
				
				$numIds = sizeof($expenseIds);
				$i = 0;
				foreach ($expenseIds as $expenseId)
				{
					$i++;
					$sql = $sql . $expenseId;
					
					if ($i<$numIds)
					{
						$sql = $sql . ", ";
					}
				}
				$sql = $sql . ")";
				
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
				
				return $result;
			}
			
			function displaySummaryExpense($result)
			{
				echo "<h4>Expense Summary</h4>";
				echo "<table border=1>
					<tr>
						<th>Payee</th>
						<th>Invoice</th>
						<th>Description</th>
						<th>Category</th>
						<th>Approval Status</th>
						<th>Amount</th>
					</tr>";
				
				while ($row = mysqli_fetch_array($result))
				{
					$approvalStatus = getApprovalStatus($row['id']);
				
					echo "<tr>" .
						"<td>" . colourTextIfApproved($row['payee'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($row['invoice_number'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($row['description'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved(getCategoryDescription($row['ledger_category']), $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($approvalStatus, $approvalStatus) . "</td>" .
						"<td>" . (colourTextIfApproved(money_format("%#1n", $row['amount']), $approvalStatus)) . "</td>" .
					"</tr>";
				}
								
				echo "</table>";
			}
			
			function validateExpenseApprovalStatus($expenseId)
			{
				if (getApprovalStatus($expenseId) != "Fully Approved")
				{
					$error = array();
					$error['inError'] = false;
					$error['warning'] = true;
					$error['message'] = "Warning - Expense not fully approved. Create payment record anyway?";
				}
				
				return $error;
			}
			
			function writePaymentMethodSelector($selectedItem)
			{
				if ($selectedItem == null )
				{
					$selectedItem = "";
				}
				
				echo "<td><select name=\"paymentMethodId\">";
				if (isset($_SESSION['paymentMethods']))
				{
					foreach ($_SESSION['paymentMethods'] as $id => $name)
					{
						echo "<option ". isSelected($name, $selectedItem) . "value=\"" . $id . "\">" . $name . "</option>";
					}
				}
				echo "</select></td>";
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
				
				echo "<input type=\"submit\" name=\"return_to_income_list\" value=\"Return to List\">";
				
				if (sizeof($_SESSION['incomeIds']) > 1 && $index > 0)
				{
					echo "<input type=\"submit\" name=\"view_previous_income\" value=\"View Previous\">";
				}
				
				if (sizeof($_SESSION['incomeIds']) > 1 && $index < (sizeof($_SESSION['incomeIds'])-1))
				{
					echo "<input type=\"submit\" name=\"view_next_income\" value=\"View Next\">";
				}
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
						"<td><input type=\"checkbox\" name=\"expenseId[]\" id=\"" . $row['id'] . "\" value=\"" . $row['id'] . "\"" . isCheckboxChecked($row['id'], $selectedExpenseIds) . "></td>" .
						"<td>" . colourTextIfApproved($row['payee'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($row['description'], $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved(getCategoryDescription($row['ledger_category']), $approvalStatus) . "</td>" .
						"<td>" . colourTextIfApproved($approvalStatus, $approvalStatus) . "</td>" .
						"<td>" . (colourTextIfApproved(money_format("%#1n", $row['amount']), $approvalStatus)) . "</td>" .
						"</tr>";
					$i++;
				}
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
				echo "<input type=\"submit\" name=\"create_payment\" value=\"Next\">";
			}
			
			function isRadioChecked($count)
			{
				$response = "";
				
				if ($count==1)
				{
					$response = "CHECKED";
				}
				
				return $response;
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