<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	include 'Expense.php';
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
	<link rel="stylesheet" type="text/css" href="datepicker.css" /> 
	<script type="text/javascript" src="datepicker.js"></script>
	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<TITLE>Create an Expense</TITLE>
	
	
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
		<h2>Add an Expense</h2>
		<form action="AddExpense.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ( $_POST['save'] )
			{
				$moveFileNeeded = false;
				$fileContent = null;
				$fileName = null;
				$fileSize = null;
				$fileType = null;
				
				$created = new DateTime($_POST['createdDate']);
				$due = new DateTime($_POST['paymentDue']);
				
				$expense = new Expense();
				$expense->SetCreated($created);
				$expense->SetPayee($_POST['payee']);
				$expense->SetFamilyCode($_POST['family']);
				$expense->SetInvoiceNumber($_POST['invoiceNumber']);
				$expense->SetDescription($_POST['description']);
				$expense->SetDueDate($due);
				$expense->SetCategory($_POST['ledgerCategoryId']);
				$expense->SetAmount($_POST['amount']);
				$expense->SetNotes($_POST['notes']);
				
				foreach ($_POST['approvers'] as $approver)
				{
					$expense->AddApproval($approver);
				}
				
				if ( $_FILES['attachmentFile']['size'] > 0 )
				{
					$tmpName  = $_FILES['attachmentFile']['tmp_name'];
					$fileType = $_FILES['attachmentFile']['type'];
					$fileContent = getFileContent($tmpName);
					$fileSize = $_FILES['attachmentFile']['size'];
					$fileName = $_FILES['attachmentFile']['name'];
					
					if (strtoupper($_SESSION['preferences']['attachment_storage']) == "FILE")
					{
						$newFilename = $_SESSION['preferences']['attachment_file_path'] . strtolower($_FILES['attachmentFile']['name']);
						$expense->SetTempFileName($tmpName);
						$expense->SetFileName($newFilename);
						$moveFileNeeded = true;
					}
					else if (strtoupper($_SESSION['preferences']['attachment_storage']) == "DATABASE")
					{
						$expense->SetImage($fileContent);
						$expense->SetImageType($fileType);
						$expense->SetImageLength($fileSize);
						$expense->SetImageName($fileName);
					}
				}
				
				$expense->Save();
				if ($expense->IsError())
				{
					displayEditExpenseForm($expense);
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
				else
				{
					echo "<h3>Expense recorded successfully.  Enter another?</h3>";
					displayAddExpenseForm();
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
			}
			else
			{
				displayAddExpenseForm();
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
			}
			
			function displayAddExpenseForm()
			{
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Date</td>";
					echo "<td><input id='createdDate' name='createdDate' class='datepicker' value='". date('d-M-Y', time()) . "'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Payee</td>";
					echo "<td> <input type=text id=\"payee\" name=\"payee\" value = \"\" </td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Family</td>";
					echo writeFamilySelector("family", null);
				echo "</tr>";
				echo "<tr>";
					echo "<td>Invoice Number</td>";
					echo "<td> <input type=text id=\"invoiceNumber\" name=\"invoiceNumber\" value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td> <input type=text id=\"description\" name=\"description\" size=50 value = \"\" </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Payment Due</td>";
					$defaultDueDate = date('d-M-Y', strtotime("+" . $_SESSION['preferences']['default_payment_terms'] . " days"));
					echo "<td><input id='paymentDue' name='paymentDue' class='datepicker' value='" . $defaultDueDate . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "E", null);
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Amount</td>";
					echo "<td> <input type=text id=\"amount\" name=\"amount\" size=10 value = \"\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Notes</td>";
					echo "<td> <input type=text id=\"notes\" name=\"notes\" size=50 value = \"\"> </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Attachment</td>";
					echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\">";
					echo "<td> <input type=file id=\"attachmentFile\" name=\"attachmentFile\" > </td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Approvers</td>";
					echo "<td>" . displayApprovers(null) . "</td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function displayEditExpenseForm($expense)
			{
				$errors = $expense->GetErrorMessages();
				
				if ($expense->IsError())
				{
					echo "<h3>Correct Expense Details</h3>";
					echo "<p>Errors:";
					foreach ($errors as $message)
					{
						echo "<br>" . $message;
					}
				}
				else
				{
					echo "<h3>Edit Expense</h3>";
				}
				
				echo "<input id='row' type='hidden' name='row' value='" . $expense->GetId() . "'>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>" . redStarIfError("created", $expense->GetErrorFields()) . "Date</td>";
					echo "<td><input id='createdDate' name='createdDate' class='datepicker' value='" . $expense->GetCreated()->format("d-M-Y") . "'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("payee", $expense->GetErrorFields()) . "Payee</td>";
					echo "<td> <input type=text id='payee' name='payee' value = '" . $expense->GetPayee() . "'</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("family", $expense->GetErrorFields()) . "Family</td>";
					echo writeFamilySelector("family", $expense->GetFamilyCode());
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("invoiceNumber", $expense->GetErrorFields()) . "Invoice Number</td>";
					echo "<td> <input type=text id='invoiceNumber' name='invoiceNumber' value = '" . $expense->GetInvoiceNumber() . "'</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("description", $expense->GetErrorFields()) . "Description</td>";
					echo "<td> <input type=text id='description' name='description' size=50 value = '". $expense->GetDescription() . "'</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("dueDate", $expense->GetErrorFields()) . "Payment Due</td>";
					echo "<td><input id='paymentDue' name='paymentDue' class='datepicker' ";
					if ($expense->GetDueDate() != null )
					{
						echo "value='" . $expense->GetDueDate()->format("d-M-Y") . "'";
					}
					echo "></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("category", $expense->GetErrorFields()) . "Category</td>";
					echo writeLedgerCategoriesDropDown("ledgerCategoryId", "E", $expense->GetCategory());
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("amount", $expense->GetErrorFields()) . "Amount</td>";
					echo "<td> <input type=text id='amount' name='amount' size=10 value = '" . $expense->GetAmount() . "'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("notes", $expense->GetErrorFields()) . "Notes</td>";
					echo "<td> <input type=text id='notes' name='notes' size=50 value = '" . $expense->GetNotes() . "'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("attachment", $expense->GetErrorFields()) . "Attachment</td>";
					echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\">";
					echo "<td> <input type=file id='attachmentFile' name='attachmentFile' ></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>" . redStarIfError("approvers", $expense->GetErrorFields()) . "Approvers</td>";
					echo "<td>" . displayApprovers($expense->GetApprovals()) . "</td>";
				echo "</tr>";
				echo "</table>";
			}
			
			function displayApprovers($selectedApprovers)
			{
				$approversText = "";
				
				// Find the number of approvers required
				$sql = "select num_value from configuration where item = 'expense_approvers_required'";
				$configResult = mysqli_query($_SESSION['databaseConnection'], $sql);
				
				$row = mysqli_fetch_array($configResult);
				$numApprovers = $row['num_value'];
				
				$sql = "select id, display_order, surname, given_names from approvers where active <= CURDATE() and (expiry is null or expiry > CURDATE()) order by display_order asc";
				$approversResult = mysqli_query($_SESSION['databaseConnection'], $sql);
	
				$i=0;
				while ($row = mysqli_fetch_array($approversResult))
				{
					$approversText = $approversText . "<input type=\"checkbox\" name=\"approvers[]\" value=\"" . $row['id'] . "\"";
					
					if ($selectedApprovers == null)
					{
						if ($i < $numApprovers)
						{
							$approversText = $approversText . " CHECKED ";
						}
					}
					else
					{
						foreach ($selectedApprovers as $thisApprover)
						{
							if ($thisApprover->GetApprover() == $row['id'])
							{
								$approversText = $approversText . " CHECKED ";
							}
						}
					}
					
					$approversText = $approversText . ">" . $row['given_names'] . " " . $row['surname'];
					$i++;
				}
				
				return $approversText;
			}
			
			function writePaymentMethodDropDown($selectedItem)
			{
				if ($selectedItem == null )
				{
					$selectedItem = "";
				}
				
				$sql = "SELECT id,name FROM payment_methods ORDER BY name ASC";
				$paymentMethodsResult = mysqli_query($_SESSION['databaseConnection'], $sql);
				
				echo "<td><select name=\"paymentMethodId\">";
				while ( $row = mysqli_fetch_array($paymentMethodsResult))
				{
					echo "<option ". isSelected( $row['name'], $selectedItem) . "value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
				}
				
				echo "</select></td>";
			}
			
			function writeAccountDropdown($selectedItem)
			{
				if ($selectedItem == null )
				{
					$selectedItem = "";
				}
				
				$sql = "SELECT id,name FROM accounts ORDER BY name ASC";
				$accountsResult = mysqli_query($_SESSION['databaseConnection'], $sql);
				
				echo "<td><select name=\"accountId\">";
				while ( $row = mysqli_fetch_array($accountsResult))
				{
					echo "<option ". isSelected( $row['name'], $selectedItem) . "value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
				}
				
				echo "</select></td>";
			}
			
			
			
			function save($createdDate, $payee, $family, $invoiceNumber, $description, $paymentDueDate, $ledgerCategoryId, $amount, $fileContent, $fileType, $fileSize, $fileName, $notes)
			{
				$success = validate($createdDate, $payee, $invoiceNumber, $description, $paymentDueDate, $ledgerCategoryId, $amount, $fileContent, $fileType, $fileSize, $fileName, $notes);
				
				if ($success)
				{
					$sql = "insert into expenses (created, payee, family_code, invoice_number, description, payment_due_date, ledger_category, amount, notes, image, image_type, image_length, image_name) " .
						"values ('" . $createdDate . "', '" . cleanString($payee) . "', ";
						
					if (strlen($family) == 0)	
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($family) . "', ";
					}
					
					$sql = $sql . "'" . $invoiceNumber . "', '" . cleanString($description) . "', '" . $paymentDueDate . "', ". $ledgerCategoryId . ", " . $amount . ", ";
				
					if ($notes == null)	
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($notes) . "', ";
					}
					
					if ($fileContent == null)
					{
						$sql = $sql . " null, null, null, null)";
					}
					else
					{
						$sql = $sql . "'" . $fileContent . "', '" . $fileType . "', " . $fileSize . ", " . "'" . $fileName . "')";
					}
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed. ' . $sql);
					$id = mysqli_insert_id($_SESSION['databaseConnection']);
				}
				
				return $id;
			}
			
			function validate($createdDate, $payee, $invoiceNumber, $description, $paymentDueDate, $ledgerCategoryId, $amount, $fileContent, $fileType, $fileSize, $fileName, $notes)
			{
				//Check file was successfully uploaded if there is an image file
				//$_FILES['userfile']['size'] > 0
				return true;
			}
			
			function getFileContent($tmpName)
			{
				$fp = fopen($tmpName, 'r');
				$fileContent = fread($fp, filesize($tmpName));
				$fileContent = addslashes($fileContent);
				fclose($fp);
				
				return $fileContent;
			}
			
			function redStarIfError($fieldName, $errorFields)
			{
				$redString = "";
				if (in_array($fieldName, $errorFields))
				{
					$redString =  "<font color='red'>*</font>";
				}
				
				return $redString;
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