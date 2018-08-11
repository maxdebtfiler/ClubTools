<?php
	class FundraisingCampaign
	{
		private $id = 0;
		private $name = null;
		private $description = null;
		private $target = 0;
		private $active = null;
		private $expiry = null;
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		private $transactions = array();
		
		function __constuct()
		{	
		}
		
		function LoadFundraisingCampaign($id)
		{
			$sql = "SELECT id, name, description, target, active, expiry FROM fundraising_campaigns WHERE id = " . $id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->name = $row['name'];
				$this->description = $row['description'];
				$this->target = $row['target'];
				$this->active = date_create_from_format("Y-m-d", $row['active']);
				$this->expiry = date_create_from_format("Y-m-d", $row['expiry']);
				
				$this->transactions = $this->loadTransactions($id);
			}
			
			return $this;
		}
		
		function GetDetailedTransactions()
		{
			$transactions = array();
			$sql = "SELECT income_date, name, description, amount FROM income WHERE id IN (SELECT income_id FROM fundraising_transactions WHERE campaign_id = " . $this->id . ")";
			$incomeResult =  mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while ($incomeRow = mysqli_fetch_array($incomeResult))
			{
				$transaction = array();
				$transaction['type'] = "I";
				$transaction['date'] = date_create_from_format("Y-m-d", $incomeRow['income_date']);
				$transaction['name'] = $incomeRow['name'];
				$transaction['description'] = $incomeRow['description'];
				$transaction['amount'] = $incomeRow['amount'];
				
				array_push($transactions, $transaction);
			}
			
			$sql = "SELECT created, payee, description, amount FROM expenses WHERE id IN (SELECT expense_id FROM fundraising_transactions WHERE campaign_id = " . $this->id . ")";
			$expenseResult =  mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while ($expenseRow = mysqli_fetch_array($expenseResult))
			{
				$transaction = array();
				$transaction['type'] = "E";
				$transaction['date'] = date_create_from_format("Y-m-d H:i:s", $expenseRow['created']);
				$transaction['name'] = $expenseRow['payee'];
				$transaction['description'] = $expenseRow['description'];
				$transaction['amount'] = $expenseRow['amount'] * -1;
				
				array_push($transactions, $transaction);
			}
			
			return $transactions;
		}
		
		private function loadTransactions($id)
		{
			$transactions = array();
			$sql = "SELECT expense_id, income_id FROM fundraising_transactions WHERE campaign_id = " . $id;
			$transResult =  mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while ($transRow = mysqli_fetch_array($transResult))
			{
				$transaction = array();
				
				if ($transRow['expense_id'] != null && $transRow['expense_id'] > 0)
				{
					$transaction["direction"] = "E";
					$transaction['id'] = $transRow['expense_id'];
				}
				else 
				{
					$transaction["direction"] = "I";
					$transaction['id'] = $transRow['income_id'];
				}
				
				array_push($transactions, $transaction);
			}
			
			return $transactions;
		}
		
		function AddTransactionToCampaign($transactionId, $isExpense)
		{
			$transaction = array();
			
			if ($isExpense)
			{
				$transaction["direction"] = "E";
				$sql = "INSERT INTO fundraising_transactions (campaign_id, expense_id, income_id) VALUES (" . $this->id . ", " . $transactionId . ", null)";
			}
			else
			{
				$transaction["direction"] = "I";
				$sql = "INSERT INTO fundraising_transactions (campaign_id, expense_id, income_id) VALUES (" . $this->id . ", null, " . $transactionId . ")";
			}
			
			$transaction["id"] = $transactionId;
			array_push($this->transactions, $transaction);
			
			// Save the transaction
			mysqli_query($_SESSION['databaseConnection'], $sql) or die;
		}
		
		function GetIncomeTransactions()
		{
			$transactionIds = array();
			
			foreach ($this->transactions as $trans)
			{
				if ($trans["direction"] == "I")
				{
					array_push($transactionIds, $trans['id']);
				}
			}
			
			return $transactionIds;
		}
		
		function GetExpenseTransactions()
		{
			$transactionIds = array();
			
			foreach ($this->transactions as $trans)
			{
				if ($trans["direction"] == "E")
				{
					array_push($transactionIds, $trans['id']);
				}
			}
			
			return $transactionIds;
		}
		
		function GetTotalRaised()
		{
			$amount = 0;
			
			if (count($this->GetIncomeTransactions() == 0) && count($this->GetExpenseTransactions) == 0)
			{
				$this->transactions = $this->loadTransactions($this->id);
			}
			
			$incomeTransactions = $this->GetIncomeTransactions();
			$expenseTransactions = $this->GetExpenseTransactions();
			
			if (count($incomeTransactions) > 0)
			{
				$sql = "SELECT sum(amount) FROM income WHERE id IN (";
				$i=0;
				
				foreach ($incomeTransactions as $transId)
				{
					$i++;
					$sql = $sql . $transId;
					
					if ($i < count($incomeTransactions))
					{
						$sql = $sql . ", ";
					}
				}
				
				$sql = $sql . ")";
				
				$result =  mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				$amount+= $row[0];
			}
			
			if (count($expenseTransactions) > 0)
			{
				$sql = "SELECT sum(amount) FROM expenses WHERE id IN (";
				$i=0;
				
				foreach ($expenseTransactions as $transId)
				{
					$i++;
					$sql = $sql . $transId;
					
					if ($i < count($expenseTransactions))
					{
						$sql = $sql . ", ";
					}
				}
				
				$sql = $sql . ")";
				
				$expenseResult =  mysqli_query($_SESSION['databaseConnection'], $sql);
				$expenseRow = mysqli_fetch_array($expenseResult);
				$amount = $amount - $expenseRow[0];
			}
			
			return $amount;
		}
		
		function Save()
		{
			$this->validate();
			
			if (!$this->hasError)
			{
				if ($this->exists())
				{
					$sql = "UPDATE fundraising_campaigns " .
					"SET name = '" . cleanString($this->name) . "', " .
					"description = '" . cleanString($this->description) . "', " .
					"target = " . $this->target . ", " .
					"active = '" . $this->active->format("Y-m-d") . "', ";
					
					if ($this->expiry == null)
					{
						$sql = $sql . "expiry = null ";
					}
					else
					{
						$sql = $sql . "expiry = '" . $this->expiry->format("Y-m-d") . "' ";
					}
					
					$sql = $sql . "WHERE id = " . $this->id;
				}
				else
				{
					$sql = "INSERT INTO fundraising_campaigns (name, description, target, active, expiry) VALUES (" .
					"'" . cleanString($this->name) . "', '" . cleanString($this->description) . "', " . $this->target . ", '" . $this->active->format("Y-m-d") . "', ";
					
					if ($this->expiry == null)
					{
						$sql = $sql . "null)";
					}
					else
					{
						$sql = $sql . "'" . $this->expiry->format("Y-m-d") . "')";
					}
				}
			
				mysqli_query($_SESSION['databaseConnection'], $sql);
				$this->id = mysqli_insert_id($_SESSION['databaseConnection']);
			}
			
			return !$this->hasError;
		}
		
		function Delete()
		{
			$this->hasError = false;
				
			//Check there are no transactions
			if (count($this->transactions) > 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "This fundraising campaign cannot be deleted as it has transactions associated with it.");
			}
			else
			{
				$transactions = $this->loadTransactions($this->id);
				if (count($this->transactions) > 0)
				{
					$this->hasError = true;
					array_push($this->errorMessages, "This fundraising campaign cannot be deleted as it has transactions associated with it.");
				}
				else
				{
					$sql = "DELETE FROM fundraising_campaigns WHERE id = " . $this->id;
					mysqli_query($_SESSION['databaseConnection'], $sql);
				}
			}
			
			return !$this->hasError;
		}
		
		function GetCampaigns($activeDate)
		{
			$campaigns = array();
			
			$sql = "SELECT id, name, description, target, active, expiry FROM fundraising_campaigns ";
			
			if ($activeDate != null)
			{
				$sql = $sql . "WHERE active <= '" . $activeDate->format("Y-m-d") . "' AND (expiry IS NULL OR expiry >= '" . $activeDate->format("Y-m-d") . "' ) ";
			}
			
			$sql = $sql . "ORDER BY active DESC";
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$i = 0;
			
			while ($row = mysqli_fetch_array($result))
			{
				$campaign = new FundraisingCampaign();
				$campaign->SetId($row['id']);
				$campaign->SetName($row['name']);
				$campaign->SetDescription($row['description']);
				$campaign->SetTarget($row['target']);
				$campaign->SetActive($row['active']);
				$campaign->SetExpiry($row['expiry']);
				
				$campaigns[$i] = $campaign;
				$i++;
			}
			
			return $campaigns;
		}
		
		private function validate()
		{
			$this->clearError();
			
			if ($this->name == null || strlen($this->name) == 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Fundraising campaign name is required.");
				array_push($this->errorFields, "name");
			}
			
			if ($this->description == null || strlen($this->description) == 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Fundraising campaign description is required.");
				array_push($this->errorFields, "description");
			}
			
			if (!is_numeric ($this->target))
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Target must be numeric.");
				array_push($this->errorFields, "target");
			}
			else
			{
				if ($this->target < 0)
				{
					$this->hasError = true;
					array_push($this->errorMessages, "Fundraising campaign must have a target greater than zero.");
					array_push($this->errorFields, "target");
				}
			}
			
			if ($this->active == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Fundraising campaign must have an active date.");
				array_push($this->errorFields, "active");
			}
			else
			{
				if ($this->expiry != null)
				{
					if ($this->expiry < $this->active)
					{
						$this->hasError = true;
						array_push($this->errorMessages, "Fundraising campaign expiry date cannot be prior to the active date.");
						array_push($this->errorFields, "expiry");
					}
				}
			}
			
			return !$this->hasError;
		}
		
		private function exists()
		{
			$exists = false;
			
			$sql = "SELECT COUNT(*) FROM fundraising_campaigns WHERE id = " . $this->id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row[0] > 0)
			{
				$exists = true;
			}
			
			return $exists;
		}
		
		function GetId()
		{
			return $this->id;
		}
		
		function SetId($id)
		{
			$this->id = $id;
		}
		
		function GetName()
		{
			return $this->name;
		}
		
		function SetName($name)
		{
			$this->name = $name;
		}
		
		function GetDescription()
		{
			return $this->description;
		}
		
		function SetDescription($description)
		{
			$this->description = $description;
		}
		
		function GetTarget()
		{
			return $this->target;
		}
		
		function SetTarget($target)
		{
			$this->target = $target;
		}
		
		function GetActive()
		{
			return $this->active;
		}
		
		function SetActive($active)
		{
			$this->active = $active;
		}
		
		function GetExpiry()
		{
			return $this->expiry;
		}
		
		function SetExpiry($expiry)
		{
			$this->expiry = $expiry;
		}
		
		function IsError()
		{
			return $this->hasError;
		}
		
		function GetErrorMessages()
		{
			return $this->errorMessages;
		}
		
		function GetErrorFields()
		{
			return $this->errorFields;
		}
		
		function ExceededTarget()
		{
			return ($this->getTotalRaised() > $this->GetTarget());
		}
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
	}


?>