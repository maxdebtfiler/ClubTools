<?php
	$_SESSION['databaseConnection'] = null;
	$_SESSION['ledgerCategories'] = null;
	$_SESSION['paymentMethodsIn'] = null;
	$_SESSION['paymentMethodsOut'] = null;
	$_SESSION['familyList'] = null;
	$_SESSION['accounts'] = null;
	$_SESSION['preferences'] = null;
	
	function init()
	{
		setDatabaseConnection();
		loadPreferences();
		loadCache();
	}
	
	function setDatabaseConnection()
	{
		$_SESSION['databaseConnection'] = mysqli_connect("localhost", "onkaswim_toolusr", "Mclarenf1", "onkaswim_clubtools") or die(mysql_error());
	}
	
	function loadPreferences()
	{
		$preferences = array();
		$sql = "SELECT name, value FROM preferences";
		$result = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while ($row = mysqli_fetch_array($result))
		{
			$preferences[$row['name']] = $row['value'];
		}
		
		$_SESSION['preferences'] = $preferences;
	}
	
	function loadCache()
	{
		// Load categories
		$categories = array();
		$sql = "SELECT id, name FROM ledger_categories";
		$categoriesResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while( $row = mysqli_fetch_array($categoriesResult))
		{
			$categories[$row['id']] = $row['name'];
		}
		
		$_SESSION['ledgerCategories'] = $categories;
		
		// Load payment methods
		$paymentMethodsIn = array();
		$paymentMethodsOut = array();
		$paymentMethodsComplex = array();
		
		$sql = "SELECT id, name, description, direction, is_complex_transaction FROM payment_methods";
		$paymentMethodsResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while( $row = mysqli_fetch_array($paymentMethodsResult))
		{
			if ($row['is_complex_transaction'] == 1)
			{
				$paymentMethodsComplex[$row['id']] = $row['name'];
			}
			else
			{
				if ($row['direction'] == "I" || $row['direction'] == "B")
				{
					$paymentMethodsIn[$row['id']] = $row['name'];
				}
				
				if ($row['direction'] == "O" || $row['direction'] == "B")
				{
					$paymentMethodsOut[$row['id']] = $row['name'];
				}
			}
			
		}
		
		$_SESSION['paymentMethodsIn'] = $paymentMethodsIn;
		$_SESSION['paymentMethodsOut'] = $paymentMethodsOut;
		$_SESSION['paymentMethodsComplex'] = $paymentMethodsComplex;
		
		// Load accounts
		$accounts = array();
		$sql = "SELECT id, name, type, description FROM accounts";
		$accountsResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while( $row = mysqli_fetch_array($accountsResult))
		{
			$accounts[$row['id']] = $row['name'];
		}
		
		$_SESSION['accounts'] = $accounts;
		
		// Load Membership Types
		$membershipTypes = array();
		$sql = "SELECT id, name FROM membership_types";
		$membershipTypesResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while( $row = mysqli_fetch_array($membershipTypesResult))
		{
			$membershipTypes[$row['id']] = $row['name'];
		}
		
		$_SESSION['membershipTypes'] = $membershipTypes;
		
		// Load family list
		$familyList = array();
		$sql = "SELECT distinct family FROM members WHERE expiry is null OR expiry > curdate() ORDER BY family ASC";
		$familyResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		while( $row = mysqli_fetch_array($familyResult))
		{
			array_push($familyList, $row['family']);
		}
		
		$_SESSION['familyList'] = $familyList;
	}
	
	function getCategoryDescription($id)
	{
		if (isset($_SESSION['ledgerCategories']))
		{
			$name = $_SESSION['ledgerCategories'][$id];
		}
		
		return $name;				
	}
	
	function isFundraisingCategory($id)
	{
		$isCampaign = false;
		
		$sql = "SELECT is_fundraising FROM ledger_categories WHERE id = " . $id;
		$result = mysqli_query($_SESSION['databaseConnection'], $sql);
		$row = mysqli_fetch_array($result);
		
		return $row['is_fundraising'];
	}
	
	function isTrainingFeeCategory($id)
	{
		$isTrainingFee = false;
		
		$sql = "SELECT is_training_fee FROM ledger_categories WHERE id = " . $id;
		$result = mysqli_query($_SESSION['databaseConnection'], $sql);
		$row = mysqli_fetch_array($result);
		
		return $row['is_training_fee'];
	}
	
	function getPaymentMethodDescription($id)
	{
		$description = null;
		
		if (isset($_SESSION['paymentMethodsIn']))
		{
			if (array_key_exists($id, $_SESSION['paymentMethodsIn']))
			{
				$description = $_SESSION['paymentMethodsIn'][$id];
			}
		}
		
		if ($description == null && isset($_SESSION['paymentMethodsOut']))
		{
			if (array_key_exists($id, $_SESSION['paymentMethodsOut']))
			{
				$description = $_SESSION['paymentMethodsOut'][$id];
			}
		}
		
		return $description;
	}
	
	function getAccountDescription($id)
	{
		$description = null;
		
		if (isset($_SESSION['accounts']))
		{
			if (array_key_exists($id, $_SESSION['accounts']))
			{
				$description = $_SESSION['accounts'][$id];
			}
		}
		
		return $description;
	}
	
	function replaceIfNull($field, $default)
	{
		$response = $default;
		
		if ($field != null)
		{
			$response = $field;
		}
		
		return $response;
	}
	
	function nullIfEmpty($field, $needsQuotes)
	{
		if (strlen($field) > 0)
		{
			if ($needsQuotes)
			{
				$field = "'" . $field . "'";
			}
		}
		else
		{
			$field = "null";
		}
		
		return $field;
	}
	
	function writePaymentMethodsSelector($fieldName, $direction, $includeNone, $selectedItem)
	{
		echo "<td width=220> <select name=\"". $fieldName . "\" >";
		
		if ($includeNone)
		{
			foreach($_SESSION['paymentMethodsComplex'] as $id=>$description)
			{
				echo "<option value=\"" . $id . "\">" . $description . "</option>";
			}
		}
		
		if ($direction == "I")
		{
			foreach($_SESSION['paymentMethodsIn'] as $id=>$description)
			{
				echo "<option ". isSelected( $id, $selectedItem) . "value=\"" . $id . "\">" . $description . "</option>";
			}
		}
		else
		{
			foreach($_SESSION['paymentMethodsOut'] as $id=>$description)
			{
				echo "<option ". isSelected( $id, $selectedItem) . "value=\"" . $id . "\">" . $description . "</option>";
			}
		}
		
		echo "</select></td>";
	}
	
	function writeAccountsSelector($fieldName, $selectedItem)
	{
		echo "<td width=220> <select name=\"". $fieldName . "\" >";
		
		foreach($_SESSION['accounts'] as $id=>$name)
		{
			echo "<option ". isSelected( $id, $selectedItem) . "value=\"" . $id . "\">" . $name . "</option>";
		}
		
		echo "</select></td>";
	}
	
	function writeMembershipTypeSelector($fieldName, $selectedItem)
	{
		echo "<td width=220> <select name=\"". $fieldName . "\" >";
		
		foreach($_SESSION['membershipTypes'] as $id=>$name)
		{
			echo "<option ". isSelected( $id, $selectedItem) . "value=\"" . $id . "\">" . $name . "</option>";
		}
		
		echo "</select></td>";
	}
	
	function writeFamilySelector($fieldName, $defaultValue)
	{
		echo "<td width=220> <select name=\"". $fieldName . "\" >";
		echo "<option " . isSelected(null, $defaultValue) . "value=\"\"></option>";

		foreach($_SESSION['familyList'] as $name)
		{
			echo "<option ". isSelected($name, $defaultValue) . "value=\"" . $name . "\">" . $name . "</option>";
		}
		
		echo "</select></td>";
	}
	
	function writeLedgerCategoriesDropDown($fieldName, $flow, $selectedItem)
	{
		if ($selectedItem == null )
		{
			$selectedItem = "";
		}
		
		$sql = "SELECT id,name,flow FROM ledger_categories ORDER BY flow ";
		
		if ($flow == "E")
		{
			$sql = $sql . "ASC";
		}
		else
		{
			$sql = $sql . "DESC";
		}	
		
		$sql = $sql . ", name ASC";
		
		$categoriesResult = mysqli_query($_SESSION['databaseConnection'], $sql);
		
		echo "<td><select name=\"" . $fieldName . "\">";
		echo "<option></option>";
		while ( $row = mysqli_fetch_array($categoriesResult))
		{
			echo "<option ". isSelected( $row['name'], $selectedItem) . "value=\"" . $row['id'] . "\">" . $row['name'] . " (". $row['flow'] . ")". "</option>";
		}
		
		echo "</select></td>";
	}
	
	function writeStatesSelector($fieldName, $selectedState)
	{
		if ($selectedState == null)
		{
			$selectedState = "";
		}
		
		echo "<td><select name=\"" . $fieldName . "\">";
		echo "<option></option>";
		echo "<option " . isSelected("SA", $selectedState) . "value='SA'>SA";
		echo "<option " . isSelected("TAS", $selectedState) . "value='TAS'>TAS";
		echo "<option " . isSelected("VIC", $selectedState) . "value='VIC'>VIC";
		echo "<option " . isSelected("NSW", $selectedState) . "value='NSW'>NSW";
		echo "<option " . isSelected("QLD", $selectedState) . "value='QLD'>QLD";
		echo "<option " . isSelected("NT", $selectedState) . "value='NT'>NT";
		echo "<option " . isSelected("WA", $selectedState) . "value='WA'>WA";
		echo "<option " . isSelected("Other", $selectedState) . "value='Other'>Other";
		echo "</select>";
	}
	
	function writeGenderSelector($fieldName, $selectedItem)
	{
		if ($selectedItem == null)
		{
			$selectedItem = "";
		}
		
		echo "<td>";
		echo "<input type='radio' name='" . $fieldName . "' value='M' " . isChecked("M", $selectedItem) . "> Male";
		echo "<input type='radio' name='" . $fieldName . "' value='F' " . isChecked("F", $selectedItem) . "> Female";
		echo "<input type='radio' name='" . $fieldName . "' value='O' " . isChecked("O", $selectedItem) . "> Other";
		echo "</td>";
	}
	
	function writePaymentNominationSelector($fieldName, $selectedNomination)
	{
		if ($selectedNomination == null)
		{
			$selectedNomination = "";
		}
		
		echo "<td><select name=\"" . $fieldName . "\">";
		echo "<option></option>";
		echo "<option " . isSelected("A", $selectedNomination) . "value='A'>Annual";
		echo "<option " . isSelected("Q", $selectedNomination) . "value='Q'>Quarterly";
		echo "<option " . isSelected("M", $selectedNomination) . "value='M'>Monthly";
		echo "<option " . isSelected("O", $selectedNomination) . "value='O'>Maximum Once Per Week";
		
		echo "</select>";
	}
	
	function writeDateSelector($fieldName, $selectedDate)
	{
		// Expiry date often is null, so leave ability to choose a blank date
		if( $selectedDate == null )
		{
			$dateArray = null;
		}
		else
		{
			$dateArray = date_parse($selectedDate);
		}
			
		echo "<td width=220> <select name=\"". $fieldName . "Day\" >";
		
		if ( $dateArray == null)
		{
			echo "<option selected> </option>";
		}
		
		for ( $i = 1 ; $i <= 31 ; $i++ )
		{
			if ( $i == $dateArray['day'] )
			{
				echo "<option selected>" . $i . "</option>";
			}
			else
			{
				echo "<option>" . $i . "</option>";
			}
				
		}
		echo "</select>";
		
		echo "<select name=\"" . $fieldName . "Month\" >";
		
		if ($dateArray == null)
		{
			echo "<option SELECTED label=\"0\" value=\"0\"> </option>";
		}
		
		echo "<option " . isSelected(1, $dateArray['month']) . " label=\"January\" value=\"1\">January</option>";
		echo "<option " . isSelected(2, $dateArray['month']) . " label=\"February\" value=\"2\">February</option>";
		echo "<option " . isSelected(3, $dateArray['month']) . " label = \"March\" value=\"3\">March</option>";
		echo "<option " . isSelected(4, $dateArray['month']) . " label=\"April\" value=\"4\">April</option>";
		echo "<option " . isSelected(5, $dateArray['month']) . " label=\"May\" value=\"5\">May</option>";
		echo "<option " . isSelected(6, $dateArray['month']) . " label=\"June\" value=\"6\">June</option>";
		echo "<option " . isSelected(7, $dateArray['month']) . " label=\"July\" value=\"7\">July</option>";
		echo "<option " . isSelected(8, $dateArray['month']) . " label=\"August\" value=\"8\">August</option>";
		echo "<option " . isSelected(9, $dateArray['month']) . " label=\"September\" value=\"9\">September</option>";
		echo "<option " . isSelected(10, $dateArray['month']) . " label=\"October\" value=\"10\">October</option>";
		echo "<option " . isSelected(11, $dateArray['month']) . " label=\"November\" value=\"11\">November</option>";
		echo "<option " . isSelected(12, $dateArray['month']) . " label=\"December\" value=\"12\">December</option>";
		echo "</select>";
		
		echo "<input type=\"text\" size=5 name=\"" . $fieldName . "Year\" value=\"" . $dateArray['year'] . "\"";
						
		echo "</td>";
	}
	
	function cleanString($string)
	{
		$newString = str_replace("'", "\\'", $string);
		return $newString;
	}
	
	function isSelected( $item, $compare )
	{
		if ( $item == $compare )
		{
			return " SELECTED ";
		}
		else
		{ 
			return "";
		}
	}
	
	function isChecked($item, $compare)
	{
		if ( $item == $compare )
		{
			return " CHECKED ";
		}
		else
		{ 
			return "";
		}
	}
	
	function checkedIfTrue($item)
	{
		if ($item)
		{
			return " CHECKED ";
		}
		else
		{
			return "";
		}
	}
	
	function isCheckboxChecked($id, $selectedList)
	{
		$result = "";
		
		if (count($selectedList)>0)
		{
			foreach($selectedList as $thisId)
			{
				if ($thisId == $id)
				{
					$result = "CHECKED";
				}
			}
		}
		
		return $result;
	}
?>