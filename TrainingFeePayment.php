<?php
	class TrainingFeePayment
	{
		private $id = 0;
		private $memberId = 0;
		private $incomeId = 0;
		private $periodType = null;
		private $periodStart = null;
		private $periodEnd = null;
		private $numCasualSessions = 0;
		private $notes = null;
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		function __constuct()
		{	
		}
		
		function LoadTrainingFeePayment($id)
		{
			$sql = "SELECT member_id, income_id, period_type, period_start, period_end, num_casual_sessions FROM training_fee_payments WHERE id = " . $id;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->memberId = $row['member_id'];
				$this->incomeId = $row['income_id'];
				$this->periodType = $row['period_type'];
				$this->periodStart = $row['period_start'];
				$this->periodEnd = $row['period_end'];
				$this->numCasualSessions = $row['num_casual_sessions'];
				$this->notes = $row['notes'];
			}
			
			return $this;
		}
		
		function GetTrainingFeePayments($memberId)
		{
			$payments = array();
			$sql = "SELECT member_id, income_id, period_type, period_start, period_end, num_casual_sessions FROM training_fee_payments WHERE member_id = " . $memberId;
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while ($row = mysqli_fetch_array($result))
			{
				$payment = new TrainingFeePayment();
				$payment->id = $row['id'];
				$payment->memberId = $memberId;
				$payment->incomeId = $row['income_id'];
				$payment->periodType = $row['payment_type'];
				$payment->periodStart = $row['payment_start'];
				$payment->periodEnd = $row['period_end'];
				$payment->numCasualSessions = $row['num_casual_sessions'];
				$payment->notes = $row['notes'];
				
				array_push($payments, $payment);
			}
			
			return $payments;
		}
		
		private function validate()
		{
			if ($this->id < 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid ID has not been provided.");
				array_push($this->errorFields, "id");
			}
			
			if ($this->memberId <= 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid Member Id has not been provided.");
				array_push($this->errorFields, "memberId");
			}
			
			if ($this->incomeId <= 0)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "A valid Income Id has not been provided.");
				array_push($this->errorFields, "incomeId");
			}
			
			if ($this->periodType != "ANNUALLY" && $this->periodType != "QUARTERLY" && $this->periodType != "MONTHLY" && $this->periodType != "CASUAL")
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Invalid Period Type. Valid values are ANNUALLY, QUARTERLY, MONTHLY and CASUAL");
				array_push($this->errorFields, "periodType");
			}
			
			return !$this->hasError;
		}
		
		function CalculatePeriodEndDate()
		{
			if ($this->periodStart != null)
			{
				$this->periodEnd = clone $this->periodStart;
						
				switch ($this->periodType)
				{
					case "ANNUALLY":
						date_add($this->periodEnd, date_interval_create_from_date_string("1 year"));
						date_add($this->periodEnd, date_interval_create_from_date_string("-1 day"));
						break;
					case "QUARTERLY":
						date_add($this->periodEnd, date_interval_create_from_date_string("3 months"));
						date_add($this->periodEnd, date_interval_create_from_date_string("-1 day"));
						break;
					case "MONTHLY":
						date_add($this->periodEnd, date_interval_create_from_date_string("1 month"));
						date_add($this->periodEnd, date_interval_create_from_date_string("-1 day"));
						break;
					case "CASUAL":
						date_add($this->periodEnd, date_interval_create_from_date_string("1 month"));
						date_add($this->periodEnd, date_interval_create_from_date_string("-1 day"));
				}
			}
		}
		
		function GetId()
		{
			return $this->id;
		}
		
		function SetId($id)
		{
			$this->id = $id;
		}
		
		function GetMemberId()
		{
			return $this->memberId;
		}
		
		function SetMemberId($memberId)
		{
			$this->memberId = $memberId;
		}
		
		function GetIncomeId()
		{
			return $this->incomeId;
		}
		
		function SetIncomeId($incomeId)
		{
			$this->incomeId = $incomeId;
		}
		
		function GetPeriodType()
		{
			return $this->periodType;
		}
		
		function SetPeriodType($periodType)
		{
			$this->periodType = $periodType;
		}
		
		function GetPeriodStart()
		{
			return $this->periodStart;
		}
		
		function SetPeriodStart($periodStart)
		{
			$this->periodStart = $periodStart;
		}
		
		function GetPeriodEnd()
		{
			return $this->periodEnd;
		}
		
		function SetPeriodEnd($periodEnd)
		{
			$this->periodEnd = $periodEnd;
		}
		
		function GetNumCasualSessions()
		{
			return $this->getNumCasualSessions;
		}
		
		function SetNumCasualSessions($numCasualSessions)
		{
			$this->numCasualSessions = $numCasualSessions;
		}
		
		function GetNotes()
		{
			return $this->notes;
		}
		
		function SetNotes($notes)
		{
			$this->notes = $notes;
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
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
	}
?>