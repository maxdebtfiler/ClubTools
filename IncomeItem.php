<?php
	class IncomeItem
	{
		private $id = 0;
		private $incomeDate = null;
		private $paymentMethod = 0;
		private $invoiceNumber = null;
		private $family = null;
		private $name = null;
		private $description = null;
		private $category = 0;
		private $amount = 0;
		private $amountEstimated = false;
		private $notes = null;
		private $followupStatus = null;
		private $trainingFees = null;
		private $advisedBy = null;
		private $followupItems = array();
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		function __constuct()
		{	
		}
		
		function LoadIncomeItem($id)
		{
			$sql = "SELECT income_date, payment_method, invoice_number, family_code, name, description, ledger_category, amount, amount_estimated, notes, advised_by, followup_status 
				FROM income WHERE id = " . $id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->incomeDate = $row['income_date'];
				$this->paymentMethod = $row['payment_method'];
				$this->invoiceNumber = $row['invoice_number'];
				$this->family = $row['family_code'];
				$this->name = $row['name'];
				$this->description = $row['description'];
				$this->category = $row['ledger_category'];
				$this->amount = $row['amount'];
				$this->amountEstimated = $row['amount_estimated'];
				$this->notes = $row['notes'];
				$this->followupStatus = $row['followup_status'];
				$this->advisedBy = $row['advised_by'];
			}
			
			// Load followup actions
			$sql = "SELECT id, income_id, followup_date, reason, action FROM followup_actions WHERE income_id = " . $id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			while ($row = mysqli_fetch_array($result))
			{
				$followup = new FollowupItem();
				$followup->SetId($row['id']);
				$followup->SetIncomeItemId($id);
				$followup->SetFollowupDate($row['followup_date']);
				$followup->SetFollowupReason($row['reason']);
				$followup->SetFollowupAction($row['action']);
				
				$this->AddFollowupItem($followup);
			}
			
			return $this;
		}
		
		function GetUnreceiptedIncomeItems()
		{
			$incomeItems = array();
			$sql = "SELECT id, income_date, payment_method, invoice_number, family_code, name, description, ledger_category, amount, amount_estimated, notes, advised_by, followup_status 
				FROM income 
				WHERE id NOT IN (SELECT income_id FROM payment_transactions WHERE income_id IS NOT NULL) ORDER BY income_date ASC";
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while($row = mysqli_fetch_array($result))
			{
				$incomeItem = new IncomeItem();
				$incomeItem->id = $row['id'];
				$incomeItem->incomeDate = $row['income_date'];
				$incomeItem->paymentMethod = $row['payment_method'];
				$incomeItem->invoiceNumber = $row['invoice_number'];
				$incomeItem->family = $row['family_code'];
				$incomeItem->name = $row['name'];
				$incomeItem->description = $row['description'];
				$incomeItem->category = $row['ledger_category'];
				$incomeItem->amount = $row['amount'];
				$incomeItem->amountEstimated = $row['amount_estimated'];
				$incomeItem->notes = $row['notes'];
				$incomeItem->followupStatus = $row['followup_status'];
				$incomeItem->advisedBy = $row['advised_by'];
				
				array_push($incomeItems, $incomeItem);
			}
			
			return $incomeItems;
		}
		
		function Save()
		{
			if ($this->Validate())
			{
				if (!$this->amountEstimated)
				{
					$formula = $this->getAmountEstimationFormula();
					if ($formula != null)
					{
						$this->estimateAmount($formula);
					}
				}
				
				if ($this->exists())
				{
					$sql = "UPDATE income SET " .
						"income_date = '" . $this->incomeDate->format("Y-m-d H:i:s") . "', " .
						"payment_method = " . $this->paymentMethod . ", " .
						"invoice_number = ";
						
					if ($this->invoiceNumber == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . $this->invoiceNumber . "', ";
					}
					
					$sql = $sql . "family_code = ";
					if ($this->family == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->family) . "', ";
					}
					
					$sql = $sql . "name = ";
					if ($this->name == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->name) . "', ";
					}
					
					$sql = $sql . "description = ";
					if ($this->description == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->description) . "', ";
					}
					
					$sql = $sql . "ledger_category = " . $this->category . ", " .
						"amount = " . $this->amount . ", ";
					
					if ($this->amountEstimated)
					{
						$sql = $sql . "amount_estimated = " . $this->amountEstimated . ", ";
					}
					else
					{
						$sql = $sql . "amount_estimated = false, ";
					}
					
					$sql = $sql . "notes = ";
					if ($this->notes == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->notes) . "', ";
					}
					
					$sql = $sql . "advised_by = ";
					if ($this->advisedBy == null)
					{
						$sql = $sql . "null ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->advisedBy) . "' ";
					}
					
					$sql = $sql . "WHERE id = " . $this->id;
					
					mysqli_query($_SESSION['databaseConnection'], $sql) or die;
				}
				else
				{
					$sql = "INSERT INTO income (income_date, payment_method, invoice_number, family_code, name, description, ledger_category, amount, amount_estimated, notes, advised_by) VALUES (" .
						"'" . $this->incomeDate->format("Y-m-d H:i:s") . "', " .
						$this->paymentMethod . ", ";
					
					if ($this->invoiceNumber == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . $this->invoiceNumber . "', ";
					}
					
					if ($this->family == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->family) . "', ";
					}
					
					if ($this->name == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->name) . "', ";
					}
					
					if ($this->description == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->description) . "', ";
					}
					
					$sql = $sql . $this->category . ", " . $this->amount . ", ";
					
					if ($this->amountEstimated)
					{
						$sql = $sql . "true, ";
					}
					else
					{
						$sql = $sql . "false, ";
					}
					
					if ($this->notes == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->notes) . "', ";
					}
					
					if ($this->advisedBy == null)
					{
						$sql = $sql . "null)";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->advisedBy) . "')";
					}
					
					mysqli_query($_SESSION['databaseConnection'], $sql);
					$this->id = mysqli_insert_id($_SESSION['databaseConnection']);
					
					// Update the IncomeIds on all linked Followup items
					$followUps = $this->followupItems;
					foreach ($followUps as $followUp)
					{
						$followUp->SetIncomeItemId($this->id);
					}
				}
				
				$followUps = $this->followupItems;
				foreach ($followUps as $followUp)
				{
					$followUp->Save();
				}
			}
		}
		
		function Delete()
		{
			$sql = "DELETE FROM income WHERE id = " . $this->id;
			mysqli_query($_SESSION['databaseConnection'], $sql);			
		}
		
		private function exists()
		{
			$exists = false;
			
			if ($this->id > 0)
			{
				$sql = "SELECT COUNT(*) FROM income WHERE id = " . $this->id;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row[0] > 0)
				{
					$exists = true;
				}
			}
			
			return $exists;
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
			
			if ($this->incomeDate == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid income date has not been provided.");
				array_push($this->errorFields, "incomeDate");
			}
			
			$today = new DateTime();
			if ($this->incomeDate > $today)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Income date cannot be in the future.");
				array_push($this->errorFields, "incomeDate");
			}
			
			if ($this->name == null && $this->description == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Name or description is required.");
				array_push($this->errorFields, "name");
			}
			
			if ($this->category == 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid ledger category is required.");
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
				array_push($this->errorMessages, "The income amount must be greater than zero.");
				array_push($this->errorFields, "amount");
			}
			
			return !$this->hasError;
		}
		
		private function estimateAmount($formula)
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
						$this->amount = $this->amount - $formulaItems[$i];
						break;
					case "+":
						$i++;
						$this->amount = $this->amount + $formulaItems[$i];
						break;
					case "*":
						$i++;
						$this->amount = $this->amount * $formulaItems[$i];
						break;
					case "/":
						$i++;
						$this->amount = $this->amount / $formulaItems[$i];
						break;
					default:
						break;
				}
				
				$i++;
			}
			
			$this->amount = round($this->amount, 2);
			$this->amountEstimated = true;
		}
			
		private function getAmountEstimationFormula()
		{
			$formula = null;
			
			$sql = "SELECT estimate_calculation FROM payment_methods WHERE id = " . $this->paymentMethod;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
	
			$row = mysqli_fetch_array($result);
			if ($row['estimate_calculation'] != null)
			{
				$formula = $row['estimate_calculation'];
			}
			
			return $formula;	
		}
		
		function AddFollowupItem($followupItem) 
		{
			$followupItem->SetIncomeItemId($this->id);
			array_push($this->followupItems, $followupItem);
		}
		
		function ClearFollowUpItems() {$this->followupItems = array();}
	
		function GetId() {return $this->id;}
		function GetIncomeDate() {return $this->incomeDate;}
		function GetPaymentMethod() {return $this->paymentMethod;}
		function GetInvoiceNumber() {return $this->invoiceNumber;}
		function GetFamily() {return $this->family;}
		function GetName() {return $this->name;}
		function GetDescription() {return $this->description;}
		function GetCategory() {return $this->category;}
		function GetAmount() {return $this->amount;}
		function GetAmountEstimated() {return $this->amountEstimated;}
		function GetNotes() {return $this->notes;}
		function GetFollowupStatus() {return $this->followupStatus;}
		function GetAdvisedBy() {return $this->advisedBy;}
		function GetFollowupItems() {return $this->followupItems;}
		function IsError() {return $this->hasError;}
		function GetErrorMessages() {return $this->errorMessages;}
		function GetErrorFields() {return $this->errorFields;}
		
		function SetId($id) {$this->id = $id;}
		function SetIncomeDate($incomeDate) {$this->incomeDate = $incomeDate;}
		function SetPaymentMethod($paymentMethod) {$this->paymentMethod = $paymentMethod;}
		function SetInvoiceNumber($invoiceNumber) {$this->invoiceNumber = $invoiceNumber;}
		function SetFamily($family) {$this->family = $family;}
		function SetName($name) {$this->name = $name;}
		function SetDescription($description) {$this->description = $description;}
		function SetCategory($category) {$this->category = $category;}
		function SetAmount($amount) {$this->amount = $amount;}
		function SetAmountEstimated($amountEstimated) {$this->amountEstimated = $amountEstimated;}
		function SetNotes($notes) {$this->notes = $notes;}
		function SetFollowupStatus($followupStatus) {$this->followupStatus = $followupStatus;}
		function SetAdvisedBy($advisedBy) {$this->advisedBy = $advisedBy;}
		function SetFollowupItems($followupItems) {$this->followupItems = $followupItems;}
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
	}
	
	class FollowupItem
	{
		private $id = 0;
		private $incomeItemId = 0;
		private $followupDate = null;
		private $followupAction = null;
		private $followupReason = null;
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		function __constuct()
		{	
		}
		
		function LoadFollowupItem($id)
		{
			$sql = "SELECT id, income_id, followup_date, reason, action FROM followup_actions WHERE id = " . $id;
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->incomeItemId = $row['income_id'];
				$this->followupDate = date_create_from_format("Y-m-d", $row['followup_date']);
				$this->followupReason = $row['reason'];
				$this->followupAction = $row['action'];
			}
			
			return $this;
		}
		
		function GetFollowupItemsForIncomeId($incomeId)
		{
			$followupItems = array();
			
			$sql = "SELECT id, income_id, followup_date, reason, action FROM followup_actions WHERE income_id = " . $incomeId;
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			while ($row = mysqli_fetch_array($result))
			{
				$item = new FollowupItem();
				$item->id = $row['id'];
				$item->incomeItemId = $incomeId;
				$item->followupDate = date_create_from_format("Y-m-d", $row['followup_date']);
				$item->followupReason = $row['reason'];
				$item->followupAction = $row['action'];
				
				array_push($followupItems, $item);
			}
			
			return $followupItems;
		}
		
		function Delete()
		{
			$sql = "DELETE FROM followup_actions WHERE id = " . $this->id;
			mysqli_query($_SESSION['databaseConnection'], $sql);
		}
		
		function Save()
		{
			if ($this->Validate())
			{
				if ($this->exists())
				{
					$sql = "UPDATE followup_actions 
						SET income_id = " . $this->incomeItemId . ", 
						followup_date = '" . $this->followupDate->format("Y-m-d") . "',
						reason = '" . cleanString($this->followupReason) . "', 
						action = ";
						
						if ($this->followupAction == null)
						{
							$sql = $sql . "null, ";
						}
						else
						{
							$sql = $sql . "'" . cleanString($this->followupAction) . "' ";
						}
						
						$sql = $sql . "WHERE id = " . $this->id;
					
					mysqli_query($_SESSION['databaseConnection'], $sql);
				}
				else
				{
					$sql = "INSERT INTO followup_actions (income_id, followup_date, reason, action) VALUES (" .
						$this->incomeItemId . ", '" . $this->followupDate->format("Y-m-d") . "', '" . cleanString($this->followupReason) . "', ";
						
					if ($this->followupAction == null)
					{
						$sql = $sql . "null, ";
					}
					else
					{
						$sql = $sql . "'" . cleanString($this->followupAction) . "')";
					}
					
					mysqli_query($_SESSION['databaseConnection'], $sql);
					$this->id = mysqli_insert_id($_SESSION['databaseConnection']);
				}
			}
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
			
			if ($this->followupAction == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A follow up action is required.");
				array_push($this->errorFields, "followupAction");
			}
			
			if ($this->followupReason == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A follow up reason is required.");
				array_push($this->errorFields, "followupReason");
			}
			
			if ($this->followupDate == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A follow up date is required.");
				array_push($this->errorFields, "followupDate");
			}
			
			return !$this->hasError;
		}
		
		private function exists()
		{
			$exists = false;
			
			if ($this->id > 0)
			{
				$sql = "SELECT COUNT(*) FROM followup_actions WHERE id = " . $this->id;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row[0] > 0)
				{
					$exists = true;
				}
			}
			
			return $exists;
		}
		
		function GetId() {return $this->id;}
		function GetIncomeItemId() {return $this->incomeItemId;}
		function GetFollowupDate() {return $this->followupDate;}
		function GetFollowupAction() {return $this->followupAction;}
		function GetFollowupReason() {return $this->followupReason;}
		function IsError() {return $this->hasError;}
		function GetErrorMessages() {return $this->errorMessages;}
		function GetErrorFields() {return $this->errorFields;}
		
		function SetId($id) {$this->id = $id;}
		function SetIncomeItemId($incomeId) {$this->incomeItemId = $incomeId;}
		function SetFollowupDate($followupDate) {$this->followupDate = $followupDate;}
		function SetFollowupAction($action) {$this->followupAction = $action;}
		function SetFollowupReason($reason) {$this->followupReason = $reason;}
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
	}
?>