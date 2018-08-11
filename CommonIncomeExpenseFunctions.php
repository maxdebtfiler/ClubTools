<?php
function displayUnreceiptedIncomeItems($selectedIncomeIds)
{
	echo "<h3>Select unreceipted income items</h3>";
	
	$sql = "SELECT id, income_date, name, description, amount, payment_method, ledger_category, amount_estimated, notes FROM income WHERE id NOT IN " .
		"(SELECT income_id FROM payment_transactions WHERE income_id IS NOT NULL) ORDER BY income_date ASC";
	$result = mysqli_query($_SESSION['databaseConnection'], $sql) or die('Error, query failed' . $sql);
	
	echo "<table border=1>
		<tr>
			<th/>
			<th>Date</th>
			<th>Name</th>
			<th>Description</th>
			<th>Category</th>
			<th>Amount</th>
		</tr>";
	
	while ($row = mysqli_fetch_array($result))
	{
		$displayDate = date_create_from_format("Y-m-d", $row['income_date']);
		
		echo "<tr>";
			echo "<td><input type=\"checkbox\" name=\"incomeIds[]\" id=\"" . $row['id'] . "\" value=\"" . $row['id'] . "\"" . isCheckboxChecked($row['id'], $selectedIncomeIds) . "></td>" .
				"<td>" . $displayDate->format("d-m-Y") . "</td>" .
				"<td>" . $row['name'] . "</td>" .
				"<td>" . $row['description'] . "</td>" .
				"<td>" . getCategoryDescription($row['ledger_category']) . "</td>" .
				"<td>" . money_format("%#1n", $row['amount']) . "</td>";
		echo "</tr>";
	}
	
	echo "</table>";
}


			

?>