<?php
	class Expense
	{
		private $id = 0;
		private $created = null;
		private $payee = null;
		private $familyCode = null;
		private $invoiceNumber = null;
		private $description = null;
		private $dueDate = null;
		private $category = 0;
		private $amount = 0;
		private $image = null;
		private $imageType = null;
		private $imageLength = 0;
		private $imageName = null;
		private $notes = null;
		private $tempFileName = null;
		private $fileName = null;
		private $approvals = array();
		private $approvalNotification = false;
		
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		private $approvalsSaveNeeded = false;
		
		function __construct()
		{	
		}
		
		function LoadExpense($id)
		{
			$sql = "SELECT created, payee, family_code, invoice_number, description, payment_due_date, ledger_category, amount, image, image_type, image_length, image_name, notes, invoice_filename
				FROM expenses
				WHERE id = " . $id;
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->created = $row['created'];
				$this->payee = $row['payee'];
				$this->familyCode = $row['family_code'];
				$this->invoiceNumber = $row['invoice_number'];
				$this->description = $row['description'];
				$this->dueDate = $row['payment_due_date'];
				$this->category = $row['ledger_category'];
				$this->amount = $row['amount'];
				$this->image = $row['image'];
				$this->imageType = $row['image_type'];
				$this->imageLength = $row['image_length'];
				$this->imageName = $row['image_name'];
				$this->notes = $row['notes'];
				$this->fileName = $row['invoice_filename'];
				
				$this->loadApprovalRecords();
			}
			
			return $this;
		}
		
		private function loadApprovalRecords()
		{
			$sql = "SELECT id FROM approvals WHERE expense_id = " . $this->id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			unset($this->approvals);
			$approvalsSaveNeeded = false;
			
			while($row = mysqli_fetch_array($result))
			{
				$approval = new Approval();
				$approval = $approval->LoadApproval($row['id']);
				array_push($this->approvals, $approval);
			}
		}
		
		function GetUnpaidExpenses()
		{
			$expenses = array();
			$sql = "SELECT id, created, payee, family_code, invoice_number, description, payment_due_date, ledger_category, amount, image, image_type, image_length, image_name, notes, invoice_filename
				FROM expenses
				WHERE id NOT IN (SELECT expense_id FROM payment_transactions WHERE expense_id IS NOT NULL)
				ORDER BY created ASC";
				
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while($row = mysqli_fetch_array($result))
			{
				$expense = new Expense();
				
				$expense->id = $row['id'];
				$expense->created = $row['created'];
				$expense->payee = $row['payee'];
				$expense->familyCode = $row['family_code'];
				$expense->invoiceNumber = $row['invoice_number'];
				$expense->description = $row['description'];
				$expense->dueDate = $row['payment_due_date'];
				$expense->category = $row['ledger_category'];
				$expense->amount = $row['amount'];
				$expense->image = $row['image'];
				$expense->imageType = $row['image_type'];
				$expense->imageLength = $row['image_length'];
				$expense->imageName = $row['image_name'];
				$expense->notes = $row['notes'];
				$expense->fileName = $row['invoice_filename'];
				
				array_push($expenses, $expense);
			}
			
			return $expenses;
		}
		
		function Save()
		{
			if ($this->Validate())
			{
				if ($this->exists())
				{
					$sql = "UPDATE expenses " .
						"SET created = '" . $this->created->format("Y-m-d H:i:s") . "', " .
						"payee = '" . cleanString($this->payee) . "', ";
						
						if ($this->familyCode == null)
						{
							$sql = $sql . "family_code = null, ";
						}
						else 
						{
							$sql = $sql . "family_code = '" . cleanString($this->familyCode) . "', ";
						}
						
						if ($this->invoiceNumber == null)
						{
							$sql = $sql . "invoice_number = null, ";
						}
						else 
						{
							$sql = $sql . "invoice_number = '" . cleanString($this->invoiceNumber) . "', ";
						}
						
						$sql = $sql . "description = '" . cleanString($this->description) . "', " .
						"payment_due_date = '" . $this->dueDate->format("Y-m-d") . "', " .
						"ledger_category = " . $this->category . ", " .
						"amount = " . $this->amount . ", ";
						
						if ($this->notes == null)
						{
							$sql = $sql . "notes = null, ";
						}
						else 
						{
							$sql = $sql . "notes = '" . cleanString($this->notes) . "', ";
						}
						
						if ($this->image == null)
						{
							$sql = $sql . "image = null, ";
						}
						else 
						{
							$sql = $sql . "image = '" . $this->image . "', ";
						}
						
						if ($this->imageType == null)
						{
							$sql = $sql . "image_type = null, ";
						}
						else 
						{
							$sql = $sql . "image_type = '" . $this->imageType . "', ";
						}
						
						if ($this->imageLength == null)
						{
							$sql = $sql . "image_length = null, ";
						}
						else 
						{
							$sql = $sql . "image_length = " . $this->imageLength . ", ";
						}
						
						if ($this->imageName == null)
						{
							$sql = $sql . "image_name = null, ";
						}
						else 
						{
							$sql = $sql . "image_name = '" . $this->imageName . "', ";
						}
						
						if ($this->fileName == null)
						{
							$sql = $sql . "invoice_filename = null, ";
						}
						else 
						{
							$sql = $sql . "invoice_filename = '" . $this->fileName . "', ";
						}
						
						$sql = $sql . "approval_notification = " . $this->approvalNotification . " ";
						$sql = $sql . "WHERE id = " . $this->id;
						
					
					if(!mysqli_query($_SESSION['databaseConnection'], $sql))
					{
						$this->hasError = true;
						array_push($this->errorMessages, "<br>Database sql error: " . $sql);
					}
				}
				else
				{
					$sql = "INSERT into expenses (created, payee, family_code, invoice_number, description, payment_due_date, ledger_category, amount, notes, image, image_type, image_length, image_name, invoice_filename) " .
						"VALUES (" .
						"'" . $this->created->format("Y-m-d H:i:s") . "', " .
						"'" . cleanString($this->payee) . "', ";
						
					if ($this->familyCode == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . cleanString($this->familyCode) . "', ";
					}
					
					if ($this->invoiceNumber == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . cleanString($this->invoiceNumber) . "', ";
					}
					
					$sql = $sql . "'" . cleanString($this->description) . "', " .
						"'" . $this->dueDate->format("Y-m-d") . "', " .
						$this->category . ", " .
						$this->amount . ", ";
						
					if ($this->notes == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . cleanString($this->notes) . "', ";
					}
					
					if ($this->image == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . $this->image . "', ";
					}
					
					if ($this->imageType == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . $this->imageType . "', ";
					}
					
					if ($this->imageLength == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . $this->imageLength . ", ";
					}
					
					if ($this->imageName == null)
					{
						$sql = $sql . "null, ";
					}
					else 
					{
						$sql = $sql . "'" . $this->imageName . "', ";
					}
					
					if ($this->fileName == null)
					{
						$sql = $sql . "null)";
					}
					else
					{
						$sql = $sql . "'" . $this->fileName . "')";
					}
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die;
					$this->id = mysqli_insert_id($_SESSION['databaseConnection']);
					
					// Move the file to its proper location if to be stored on file system
					if (strtoupper($_SESSION['preferences']['attachment_storage']) == "FILE" && $this->fileName != null)
					{
						move_uploaded_file($this->tempFileName,  $this->fileName);
					}
					
					$this->sendApprovalEmails();
				}
			}
			
			return !$this->IsError();
		}
		
		function Validate()
		{
			$this->clearError();
			
			if ($this->id < 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid ID has not been provided.");
				array_push($this->errorFields, "id");
			}	
			
			if ($this->created == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid created date has not been provided.");
				array_push($this->errorFields, "created");
			}
			
			$today = new DateTime();
			if ($this->created > $today)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Created date cannot be in the future.");
				array_push($this->errorFields, "created");
			}
			
			if ($this->payee == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Payee is required.");
				array_push($this->errorFields, "payee");
			}
			
			if ($this->description == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Description is required.");
				array_push($this->errorFields, "description");
			}
			
			if ($this->dueDate == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid due date has not been provided.");
				array_push($this->errorFields, "dueDate");
			}
			
			if ($this->category == null || $this->category == 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A category has not been provided.");
				array_push($this->errorFields, "category");
			}
			
			if ($this->amount == 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "An amount is required.");
				array_push($this->errorFields, "amount");
			}
			else if ($this->amount < 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "The expense amount must be greater than zero.");
				array_push($this->errorFields, "amount");
			}
			
			//Check we have at least x approval records
			$requiredApprovals = $_SESSION['preferences']['expense_approvers_required'];
			if (count($this->approvals) < $requiredApprovals)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "At least " . $requiredApprovals . " approvers are required.");
				array_push($this->errorFields, "approvers");
			}
			
			// Check we are not allowing an approver to approve their own payment
			if ($this->payeeMatchesApprover($this->payee, $this->approvals))
			{
				$this->hasError = true;
				array_push($this->errorMessages, "One of the approvers is the same as the payee.  Approvers are not allowed to approve their own payment.");
				array_push($this->errorFields, "approvers");
			}
			
			return !$this->hasError;
		}
		
		function payeeMatchesApprover($payee, $approvals)
		{
			$match = false;
						
			foreach ($approvals as $approval)
			{
				$sql = "SELECT surname FROM approvers WHERE id = " . $approval->GetApprover();
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if (stripos($payee, $row['surname']) !== false)
				{
					$match = true;
				}
			}
			
			return $match;
		}
		
		function Delete()
		{
			$sql = "DELETE FROM expenses WHERE id = " . $this->id;
			mysqli_query($_SESSION['databaseConnection'], $sql);

			foreach ($this->approvals as $approval)
			{
				$approval->Delete();
			}
		}
		
		private function sendApprovalEmails()
		{
			// Load up the approval details
			foreach ($this->approvals as $approval)
			{
				$approval->Load();
				$approval->Save($this->id);
				$msgSuccess = $this->sendApprovalEmail($approval);
			}
		}
		
		private function sendApprovalEmail($approval)
		{
			// Add the attachment
			$mail = new PHPMailer();
		
			$mail->IsSMTP();
			$mail->Host = $_SESSION['preferences']['mail_host'];
			$mail->SetFrom = $_SESSION['preferences']['mail_from'];
			$mail->From = $_SESSION['preferences']['mail_from'];
			$mail->FromName = $_SESSION['preferences']['mail_from_display_name'];
			
			$mail->AddAddress($approval->GetEmail());
			
			$mail->Subject = "APPROVAL REQUIRED " . $this->payee . " Due " . $this->dueDate->format("d-M-Y");
			$mail->IsHTML(true);
			$mail->Body = $this->createApprovalEmailBody($approval);
			$mail->WordWrap = 50;
			
			//Deal with the attachment
			if ($_SESSION['preferences']['attachment_storage'] == "DATABASE")
			{
				if ($image != null)
				{
					$mail->AddStringAttachment($image, $name, "base64", $type, "attachment");
				}
			}
			else if ($_SESSION['preferences']['attachment_storage'] == "FILE")
			{
				$mail->AddAttachment($this->fileName);
			}
							
			if (!$mail->Send())
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Email send error: " . $mail->ErrorInfo);
				array_push($this->errorFields, "email");
			}
		}
		
		private function createApprovalEmailBody($approval)
		{
			$body = "<p style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\">Hi " . $approval->GetGivenNames() . ",</p>" .
				"<p style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\">The following payment is awaiting your approval:</p>" .
				"<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px; width: 500px;\">" .
				"<tbody>" .
					"<tr>" .
						"<td>Payee</td>" .
						"<td>" . $this->payee . "</td>" .
					"</tr>" .
					"<tr>" .
						"<td>Invoice Number</td>" .
						"<td>" . replaceIfNull($this->invoiceNumber, "None") . "</td>" .
					"</tr>" .
					"<tr>" .
						"<td>Description</td>" .
						"<td>" . replaceIfNull($this->description, "None") . "</td>" .
					"</tr>" .
					"<tr>" .
						"<td>Category</td>" .
						"<td>" . getCategoryDescription($this->category) . "</td>" .
					"</tr>" .
					"<tr>" .
						"<td>Due Date</td>" .
						"<td>" . $this->dueDate->format("d-M-Y") . "</td>" .
					"</tr>" .
					"<tr>" .
						"<td><span style=\"color:#0000CD;\"><strong>Amount</strong></span></td>" .
						"<td><span style=\"color:#0000CD;\"><strong>" . money_format("%#1n", $this->amount) . "</strong></span></td>" .
					"</tr>" .
				"</tbody>" .
				"</table>" .
				"<p><span style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\">Additional Notes: " . replaceIfNull($this->notes, "None") . "&nbsp;</span></p>" .
				"<p><span style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\"><a href=\"" . $_SESSION['preferences']['domain'] . "/ClubTools/ApproveExpenseFromEmail.php?mode=fast&id=" . $approval->GetId() . "\">Click to Fast Approve&nbsp;</a></span></p>" .
				"<p><span style=\"color: rgb(51, 51, 51); font-family: sans-serif, Arial, Verdana, 'Trebuchet MS'; font-size: 13px; line-height: 20.8px;\"><a href=\"" . $_SESSION['preferences']['domain'] . "/ClubTools/ApproveExpenseFromEmail.php?mode=reviewinvoice&id=" . $approval->GetId() . "\">Click to review then approve or reject&nbsp;</a></span></p>";
				
			return $body;
		}
			
		private function exists()
		{
			$exists = false;
			
			if ($this->id > 0)
			{
				$sql = "SELECT COUNT(*) FROM expenses WHERE id = " . $this->id;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row[0] > 0)
				{
					$exists = true;
				}
			}
			
			return $exists;
		}
		
		function AddApproval($approverId)
		{
			$approval = new Approval();
			$approval->SetApprover($approverId);
			$approval->SetRequested(new DateTime());
			
			array_push($this->approvals, $approval);
			$approvalsSaveNeeded = true;
		}
		
		function DeleteApproval($approverId)
		{
			for ($i=0; $i < count($this->approvals); $i++)
			{
				$approval = $this->approvals[$i];
				if ($approval->GetApprover() == $approverId)
				{
					unset($this->approvals[$i]);
					$approvalsSaveNeeded = true;
				}
			}
		}
		
		function GetId() {return $this->id;}
		function GetCreated() {return $this->created;}
		function GetPayee() {return $this->payee;}
		function GetFamilyCode() {return $this->familyCode;}
		function GetInvoiceNumber() {return $this->invoiceNumber;}
		function GetDescription() {return $this->description;}
		function GetCategory() {return $this->category;}
		function GetAmount() {return $this->amount;}
		function GetImage() {return $this->image;}
		function GetImageType() {return $this->imageType;}
		function GetImageLength() {return $this->imageLength;}
		function GetImageName() {return $this->imageName;}
		function GetTempFileName() {return $this->tempFileName;}
		function GetFileName() {return $this->fileName;}
		function GetDueDate() {return $this->dueDate;}
		function GetNotes() {return $this->notes;}
		function GetApprovalNotification() {return $this->approvalNotification;}
		function GetApprovals() {return $this->approvals;}
		function IsError() {return $this->hasError;}
		function GetErrorMessages() {return $this->errorMessages;}
		function GetErrorFields() {return $this->errorFields;}
		
		function SetId($id) {$this->id = $id;}
		function SetCreated($created) {$this->created = $created;}
		function SetPayee($payee) {$this->payee = $payee;}
		function SetFamilyCode($familyCode) {$this->familyCode = $familyCode;}
		function SetInvoiceNumber($invoiceNumber) {$this->invoiceNumber = $invoiceNumber;}
		function SetDescription($description) {$this->description = $description;}
		function SetDueDate($dueDate) {$this->dueDate = $dueDate;}
		function SetCategory($category) {$this->category = $category;}
		function SetAmount($amount) {$this->amount = $amount;}
		function SetImage($image) {$this->image = $image;}
		function SetImageType($imageType) {$this->imageType = $imageType;}
		function SetImageLength($imageLength) {$this->imageLength = $imageLength;}
		function SetImageName($imageName) {$this->imageName = $imageName;}
		function SetTempFileName($tempFileName) {$this->tempFileName = $tempFileName;}
		function SetFileName($fileName) {$this->fileName = $fileName;}
		function SetApprovals($approvals) {$this->approvals = $approvals;}
		function SetNotes($notes) {$this->notes = $notes;}
		function SetApprovalNotification($approvalNotification) {$this->approvalNotification = $approvalNotification;}
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
	}
	
	class Approval
	{
		private $id = 0;
		private $approver = 0;
		private $requested = null;
		private $denied = null;
		private $approved = null;
		private $comment = null;
		private $givenNames = null;
		private $surname = null;
		private $email = null;
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		function __constuct()
		{	
		}
		
		function LoadApproval($id)
		{
			$sql = "SELECT expense_id, approver_id, requested, approved, denied, comment FROM approvals WHERE id = " . $id;
			 
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->approver = $row['approver_id'];
				$this->requested = $row['requested'];
				$this->approved = $row['approved'];
				$this->denied = $row['denied'];
				$this->comment = $row['comment'];
			}
			
			return $this;
		}
		
		function Load()
		{
			$sql = "SELECT expense_id, approver_id, requested, approved, denied, comment FROM approvals WHERE id = " . $this->id;
			 
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->approver = $row['approver_id'];
				$this->requested = $row['requested'];
				$this->approved = $row['approved'];
				$this->denied = $row['denied'];
				$this->comment = $row['comment'];
			}
			
			if ($this->approver > 0)
			{
				$sql = "SELECT id, given_names, surname, email FROM approvers WHERE id = " . $this->approver;
				$approverResult = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($approverResult);
				
				if ($row != null)
				{
					$this->givenNames = $row['given_names'];
					$this->surname = $row['surname'];
					$this->email = $row['email'];
				}
			}
		}
		
		function Save($expenseId)
		{
			$sql = "INSERT into approvals (expense_id, approver_id, requested, approved, denied, comment)" .
					"VALUES (" . $expenseId . ", " . $this->approver . ", now(), null, null, null)";
					
			mysqli_query($_SESSION['databaseConnection'], $sql);
			$id = mysqli_insert_id($_SESSION['databaseConnection']);
			
			$this->id = $id;
			
			return $id;
		}
		
		function Delete()
		{
			$sql = "DELETE from approvals WHERE id = " . $this->id;
			mysqli_query($_SESSION['databaseConnection'], $sql);
		}
		
		function GetId() {return $this->id;}
		function GetApprover() {return $this->approver;}
		function GetRequested() {return $this->requested;}
		function GetDenied() {return $this->denied;}
		function GetApproved() {return $this->approved;}
		function GetComment() {return $this->comment;}
		function GetGivenNames() {return $this->givenNames;}
		function GetSurname() {return $this->surname;}
		function GetEmail() {return $this->email;}
		function IsError() {return $this->hasError;}
		function GetErrorMessages() {return $this->errorMessages;}
		function GetErrorFields() {return $this->errorFields;}
		
		function SetId($id) {$this->id = $id;}
		function SetApprover($approver) {$this->approver = $approver;}
		function SetRequested($requested) {$this->requested = $requested;}
		function SetDenied($denied) {$this->denied = $denied;}
		function SetApproved($approved) {$this->approved = $approved;}
		function SetComment($comment) {$this->comment = $comment;}
		function SetGivenNames($givenNames) {$this->givenNames = $givenNames;}
		function SetSurname($surname) {$this->surname = $surname;}
		function SetEmail($email) {$this->email = $email;}
	}
?>