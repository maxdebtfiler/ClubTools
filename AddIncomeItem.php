<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
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
	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Create an Income Item</TITLE>
	
	
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
					<div id="NewsDate"><a href="index.php?news&nid=1">08-05-2010</a></div>
					<div id="NewsOverview"><a href="index.php?news&nid=1">This website is still under construction, please visit us later!</a></div>
				</div>
				<div style="clear:both;"></div>
			</p>
		</div>
	</div>

	<div id="colTwo">
		<h2>Create an Income Item</h2>
		<form action="AddIncomeItem.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['save'])
			{
				$incomeDate = $_POST['incomeDateYear'] . "-" . $_POST['incomeDateMonth'] . "-" . $_POST['incomeDateDay'];
				$date = new DateTime($incomeDate);
				
				$incomeRecord = array();
				$incomeRecord['incomeDate'] = $date;
				$incomeRecord['paymentMethod'] = $_POST['paymentMethod'];
				$incomeRecord['invoiceNumber'] = $_POST['invoiceNumber'];
				$incomeRecord['family'] = $_POST['family'];
				$incomeRecord['name'] = $_POST['name'];
				$incomeRecord['description'] = $_POST['description'];
				$incomeRecord['category'] = $_POST['ledgerCategoryId'];
				$incomeRecord['amount'] = $_POST['amount'];
				$incomeRecord['notes'] = $_POST['notes'];
				$incomeRecord['followup'] = $_POST['followup'];
				$incomeRecord['advisedBy'] = $_POST['advisedBy'];

				if (save($incomeRecord))
				{
					if (isFundraisingCategory($incomeRecord['category']))
					{
						displaySelectFundraisingCampaignForm($incomeRecord);
					}
					
					if ($incomeRecord['followup'])
					{
						displayFollowUpForm($incomeRecord);
					}
				}
			}
			else if ($_POST['saveandreceipt'])
			{
				$_SESSION['receiptRequired'] = true;
				
				$incomeDate = $_POST['incomeDateYear'] . "-" . $_POST['incomeDateMonth'] . "-" . $_POST['incomeDateDay'];
				$date = new DateTime($incomeDate);
				
				$incomeRecord = array();
				$incomeRecord['incomeDate'] = $date;
				$incomeRecord['paymentMethod'] = $_POST['paymentMethod'];
				$incomeRecord['invoiceNumber'] = $_POST['invoiceNumber'];
				$incomeRecord['family'] = $_POST['family'];
				$incomeRecord['name'] = $_POST['name'];
				$incomeRecord['description'] = $_POST['description'];
				$incomeRecord['category'] = $_POST['ledgerCategoryId'];
				$incomeRecord['amount'] = $_POST['amount'];
				$incomeRecord['notes'] = $_POST['notes'];
				$incomeRecord['followup'] = $_POST['followup'];
				$incomeRecord['advisedBy'] = $_POST['advisedBy'];

				if (save($incomeRecord))
				{
					if (isFundraisingCategory($incomeRecord['category']))
					{
						displaySelectFundraisingCampaignForm($incomeRecord);
					}
					
					if ($incomeRecord['followup'])
					{
						displayFollowUpForm($incomeRecord);
					}
					else
					{
						displayIncomeItemsSummary($incomeRecord);
						showBlankReceiptForm($incomeRecord);
					}
					
				}
			}
			else if ($_POST['savereceipt'])
			{
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
				unset($_SESSION['receiptRequired']);
			}
			else if ($_POST['cancelreceipt'])
			{
				
			}
			else if ($_POST['savefollowup'])
			{
				$followUpRecord = array();
				$followUpRecord['followUpDate'] = new DateTime( $_POST['followDateYear'] . "-" . $_POST['followDateMonth'] . "-" . $_POST['followDateDay'] );
				$followUpRecord['followUpReason'] = $_POST['followUpReason'];
				$followUpRecord['followUpNeeded'] = $_POST['followUpNeeded'];
				
				$error = validateFollowUpItem($followUpRecord);
				if ($error['inError'])
				{
					echo "<h3>" . $error["message"] . "</h3>";
					showFollowupFormForCorrection($followUpRecord);
				}
				else
				{
					saveFollowUpItem($followUpRecord);
					
					// If receipt is required - then go ahead and create it
					if ($_SESSION['receiptRequired'])
					{
						$incomeRecord = createIncomeRecordFromIncomeId($_SESSION['incomeItemId']);
						displayIncomeItemsSummary($incomeRecord);
						showBlankReceiptForm($incomeRecord);
					}
				}
			}
			else if ($_POST['save_fundraising_link'])
			{
				if ($_POST['fundRaisingCampaign'])
				{
					$campaign = new FundraisingCampaign();
					$campaign->SetId($_POST['fundRaisingCampaign']);
					$campaign->AddTransactionToCampaign($_SESSION['incomeItemId'], false);
				}
				
			}
			else if ($_POST['cancelfollowup'])
			{
				
			}
			else
			{
				showBlankForm();
				showStandardButtons();
			}
			
			function validateFollowUpItem($followUpRecord)
			{
				$error = array();
				$error["inError"] = false;
				
				$today = new DateTime();
				
				if ($followUpRecord['followUpDate'] > $today)
				{
					$error["inError"] = true;
					$error["errorType"] = "ERROR";
					$error["field"] = "followUpDate";
					$error["message"] = "Date recorded for the follow up cannot be in the future.";
				}
				else if (strlen($followUpRecord['followUpNeeded']) == 0)
				{
					$error["inError"] = true;
					$error["errorType"] = "ERROR";
					$error["field"] = "followUpNeeded";
					$error["message"] = "Follow up needed description must be entered.";
				}
				else if (strlen($followUpRecord['followUpReason']) == 0)
				{
					$error["inError"] = true;
					$error["errorType"] = "ERROR";
					$error["field"] = "followUpReason";
					$error["message"] = "Follow up reason must be entered.";
				}
				
				return $error;
			}
			
			function saveFollowUpItem($followUpRecord)
			{
				$sql = "INSERT INTO followup_actions (income_id, followup_date, action, reason) VALUES (" .
					$_SESSION['incomeItemId'] . ", " .
					"'" . $followUpRecord['followUpDate']->format('Y-m-d') . "', " .
					"'" . $followUpRecord['followUpReason'] . "', " .
					"'" . $followUpRecord['followUpNeeded'] . "')";
				
				mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
			}
			
			function showFollowupFormForCorrection($followUpRecord)
			{
				$incomeRecord = createIncomeRecordFromIncomeId($_SESSION['incomeItemId']);
				displayIncomeItemsSummary($incomeRecord);
				
				echo "<h2>Enter Follow Up Needed</h2>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Follow Up Date</td>";
					echo "<td>" . writeDateSelector("followDate", $followUpRecord['followUpDate']) . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Reason for Follow Up</td>";
					echo "<td><input type=text size=50 id=\"followUpReason\" name=\"followUpReason\" value = \"" . $followUpRecord['followUpReason'] . "\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow Up Needed</td>";
					echo "<td><input type=text size=50 id=\"followUpNeeded\" name=\"followUpNeeded\" value = \"" . $followUpRecord['followUpNeeded'] . "\" </td>";
				echo "</tr>";
				
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"savefollowup\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"cancelfollowup\" value=\"Cancel\">";
			}
			
			function displaySelectFundraisingCampaignForm($incomeRecord)
			{
				displayIncomeItemsSummary($incomeRecord);
				
				echo "<h2>Select fundraising campaign</h2>";
				$fc = new FundraisingCampaign();
				$today = new DateTime();
				$currentCampaigns = $fc->GetCampaigns($today);
				
				echo "<select name='fundRaisingCampaign'>";
				foreach ($currentCampaigns as $c)
				{
					echo "<option value='" . $c->GetId() . "'>" . $c->GetName() . "</option>";
				}
				echo "</select>";
				
				echo "<input type=\"submit\" name=\"save_fundraising_link\" value=\"Save\">";
			}
			
			function displayFollowUpForm($incomeRecord)
			{
				displayIncomeItemsSummary($incomeRecord);
				
				echo "<h2>Enter Follow Up Needed</h2>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Follow Up Date</td>";
					echo writeDateSelector("followDate", date('m/d/Y h:i:s a', time()));
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
				
				echo "<input type=\"submit\" name=\"savefollowup\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"cancelfollowup\" value=\"Cancel\">";
			}
			
			function displayIncomeItemsSummary($incomeRecord)
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
					echo "<td>" . $incomeRecord['incomeDate']->format("d-m-Y") . "</td>";
					echo "<td>" . getPaymentMethodDescription($incomeRecord['paymentMethod']) . "</td>";
					echo "<td>" . $incomeRecord['name'] . "</td>";
					echo "<td>" . $incomeRecord['description'] . "</td>";
					echo "<td>" . money_format("%#1n", $incomeRecord['amount']) . "</td>";
				echo "</tr></table>";
			}
			
			function showBlankReceiptForm($incomeItem)
			{
				echo "<h2>Enter Bank Receipt Details</h2>";
				
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Bank Date</td>";
					writeDateSelector("bankDate", $incomeItem['incomeDate']->format('Y-m-d H:i:s'));
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, $incomeItem['paymentMethod']);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payer</td>";
					echo "<td><input type=text id=\"payer\" name=\"payer\" value = \"" . $incomeItem['name'] . "\" </td>";
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
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=10 value = \"" . $incomeItem['amount'] . "\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id=\"notes\" name=\"notes\" size=50 value = \"\" </td>";
				echo "</tr>";
				
				echo "</table>";
				
				echo "<input type=\"submit\" name=\"savereceipt\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"cancelreceipt\" value=\"Cancel\">";
			}
			
			function showReceiptFormForCorrection($receipt)
			{
				
			}
			
			function createIncomeRecordFromIncomeId($incomeId)
			{
				$incomeItem = array();
				$sql = "SELECT income_date, payment_method, invoice_number, family_code, name, description, ledger_category, amount, amount_estimated, notes, advised_by, followup_status " .
					"FROM income " .
					"WHERE id = " . $incomeId;
				
				$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed');
				$row = mysqli_fetch_array($result);
				
				$incomeItem['incomeDate'] = new DateTime($row['income_date']);
				$incomeItem['paymentMethod'] = $row['payment_method'];
				$incomeItem['invoiceNumber'] = $row['invoice_number'];
				$incomeItem['family'] = $row['family_code'];
				$incomeItem['name'] = $row['name'];
				$incomeItem['description'] = $row['description'];
				$incomeItem['category'] = $row['ledger_category'];
				$incomeItem['amount'] = $row['amount'];
				$incomeItem['notes'] = $row['notes'];
				$incomeItem['followup'] = $row['followup_status'];
				$incomeItem['advisedBy'] = $row['advised_by'];
				
				return $incomeItem;
			}
			
			function save($incomeRecord)
			{
				$success = true;
				
				$error = validate($incomeRecord);
				
				if ($error["inError"])
				{
					$success = false;
					echo "<h3>" . $error["message"] . "</h3>";
					showFormForCorrection($incomeRecord);
					showStandardButtons();
				}
				else
				{
					$amount = $incomeRecord['amount'];
					$estimationFormula = getAmountEstimationFormula($incomeRecord['paymentMethod']);
					$estimatedAmount = false;
					
					if ($estimationFormula != null)
					{
						$incomeRecord['amount'] = estimateAmount($amount, $estimationFormula);
						$incomeRecord['estimatedAmount'] = true;
					}
					
					$id = saveIncomeItem($incomeRecord);
					$_SESSION['incomeItemId'] = $id;
				}
				
				return $success;
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
					$sql = $sql . "'" . $receipt['bankDate']->format('Y-m-d H:i:s') . "', " . $receipt['paymentMethod'] . ", '" . $receipt['payer'] . "', ";
					$sql = $sql . $receipt['account'] . ", " . $receipt['amount'] . ", ";
					$sql = $sql . nullIfEmpty($receipt['receiptNumber'], true) . ", " . nullIfEmpty($receipt['notes'], true) . ")";
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
					$receiptId = mysqli_insert_id($_SESSION['databaseConnection']);
					
					if ($receiptId > 0)
					{
						if($_SESSION['incomeItemId'])
						{
							$sql = "INSERT INTO payment_transactions (income_id, receipt_id) VALUES (" . $_SESSION['incomeItemId'] . ", " . $receiptId . ")";
							mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
						}
						
						foreach ($_SESSION['expenseIds'] as $expenseId)
						{
							$sql = "INSERT INTO payment_transactions (expense_id, receipt_id) VALUES (" . $expenseId . ", " . $receiptId . ")";
							mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
						}
					}
				}
			}
			
			function estimateAmount($amount, $formula)
			{
				$formulaItems = explode( "|", $formula);
				$i = 0;
				
				while ($i < count($formulaItems))
				{
					$element = $formulaItems[$i];
					
					switch ($element)
					{
						case "-":
							$i++;
							$amount = $amount - $formulaItems[$i];
							break;
						case "+":
							$i++;
							$amount = $amount + $formulaItems[$i];
							break;
						case "*":
							$i++;
							$amount = $amount * $formulaItems[$i];
							break;
						case "/":
							$i++;
							$amount = $amount / $formulaItems[$i];
							break;
						default:
							break;
					}
					
					$i++;
				}
				
				$amount = round($amount, 2);
				
				return $amount;
			}
			
			function getAmountEstimationFormula($paymentMethod)
			{
				$formula = null;
				
				$sql = "SELECT estimate_calculation FROM payment_methods WHERE id = " . $paymentMethod;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
		
				$row = mysqli_fetch_array($result);
				if ($row['estimate_calculation'] != null)
				{
					$formula = $row['estimate_calculation'];
				}
				
				return $formula;	
			}
			
			function saveIncomeItem($incomeRecord)
			{
				$id = 0;
				
				$sql = "INSERT INTO income (income_date, payment_method, invoice_number, family_code, name, description, ledger_category, amount, amount_estimated, notes, advised_by) VALUES (";
				$sql = $sql . "'" . $incomeRecord['incomeDate']->format('Y-m-d H:i:s') . "', " . $incomeRecord['paymentMethod'] . ", ";
				
				if ($incomeRecord['invoiceNumber'] == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $incomeRecord['invoiceNumber'] . "', ";
				}
				
				if ($incomeRecord['family'] == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . cleanString($incomeRecord['family']) . "', ";
				}
				
				if ($incomeRecord['name'] == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . cleanString($incomeRecord['name']) . "', ";
				}
				
				if ($incomeRecord['description'] == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . cleanString($incomeRecord['description']) . "', ";
				}
				
				$sql = $sql . $incomeRecord['category'] . ", " . $incomeRecord['amount'] . ", ";
				
				if ($incomeRecord['estimatedAmount'])
				{
					$sql = $sql . "true, ";
				}
				else
				{
					$sql = $sql . "false, ";
				}
				
				if ($incomeRecord['notes'] == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . cleanString($incomeRecord['notes']) . "', ";
				}
				
				if ($incomeRecord['advisedBy'] == null)
				{
					$sql = $sql . "null)";
				}
				else
				{
					$sql = $sql . "'" . $incomeRecord['advisedBy'] . "')";
				}
				
				mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
				$id = mysqli_insert_id($_SESSION['databaseConnection']);
				
				return $id;
			}
			
			function validate($incomeRecord)
			{
				$error = array();
				$error["inError"] = false;
				
				$today = new DateTime();
				
				if ($incomeRecord['incomeDate'] > $today)
				{
					$error["inError"] = true;
					$error["errorType"] = "ERROR";
					$error["field"] = "incomeDate";
					$error["message"] = "Date recorded for the income item cannot be in the future.";
				}
				
				return $error;
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
			
			function showFormForCorrection($incomeRecord)
			{
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Date</td>";
					writeDateSelector("incomeDate", $incomeRecord['incomeDate']->format('Y-m-d H:i:s'));
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, $incomeRecord['paymentMethod']);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Invoice Number</td>";
					echo "<td><input type=text id=\"invoiceNumber\" name=\"invoiceNumber\" value = \"" . $incomeRecord['invoiceNumber'] . "\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Family</td>";
					echo writeFamilySelector("family", $incomeRecord['family']);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td><input type=text id=\"name\" name=\"name\" value = \"" . $incomeRecord['name'] . "\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input type=text id=\"description\" name=\"description\" size=50 value = \"" . $incomeRecord['description'] . "\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "I", $incomeRecord['category']);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=10 value = \"" . $incomeRecord['amount'] . "\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id=\"notes\" name=\"notes\" size=50 value = \"" . $incomeRecord['notes'] . "\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow up Required?</td>";
					echo "<td><input type=\"checkbox\" name=\"followup\" value=\"true\"" . checkedIfTrue($incomeRecord['followup']) . "></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Advised By</td>";
					echo "<td><input type=text id=\"advisedBy\" name=\"advisedBy\" value = \"" . $incomeRecord['advisedBy'] . "\" </td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function showStandardButtons()
			{
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				echo "<input type=\"submit\" name=\"saveandreceipt\" value=\"Save and Create Receipt\">";
			}
			
			function showBlankForm()
			{
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Date</td>";
					writeDateSelector("incomeDate", date('m/d/Y h:i:s a', time()));
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Method</td>";
					writePaymentMethodsSelector("paymentMethod", "I", true, null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Invoice Number</td>";
					echo "<td><input type=text id=\"invoiceNumber\" name=\"invoiceNumber\" value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Family</td>";
					echo writeFamilySelector("family", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td><input type=text id=\"name\" name=\"name\" value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input type=text id=\"description\" name=\"description\" size=50 value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "I", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=10 value = \"\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td><input type=text id=\"notes\" name=\"notes\" size=50 value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Follow up Required?</td>";
					echo "<td><input type=\"checkbox\" name=\"followup\" value=\"true\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Advised By</td>";
					echo "<td><input type=text id=\"advisedBy\" name=\"advisedBy\" value = \"\" </td>";
				echo "</tr>";
				
				echo "</table>";
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