<?php include 'SecurityFunctions.php';
	session_start();
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
	<TITLE>Maintain Budget Categories</TITLE>
	
	
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
		$con = mysql_connect("localhost", "artista9_swim", "Mclarenf1") or die(mysql_error());
		mysql_select_db("artista9_clubmgmt") or die(mysql_error());
		
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
				<li><a href="ViewRecords.php">Onne</a></li>
				<li><a href="AddSwim.php">Twwo</a></li>
				<li><a href="MaintenanceMenu.php">Maintenance</a></li>
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
		<h2>Maintain Budget Categories</h2>
		<form action="MaintainBudgetCategories.php" method="post">
		
		<?php
		
			// Only don't want to display the list if we are saving and there has been a validation error
			$success = true;
			
			if ( $_POST['add'] || $_POST['edit'] )
			{
				$displaySaveButton = true;
			}
			else
			{
				$displaySaveButton = false;
			}
			
			if ( $_POST['save'] )
			{
				$newActive = $_POST['newActiveYear'] . "-" . $_POST['newActiveMonth'] . "-" . $_POST['newActiveDay'];
				
				if ($_POST['newExpiryYear'] == null && $_POST['newExpiryMonth'] == 0 && $_POST['newExpiryDay'] == null )
				{
					$newExpiry = null;
				}
				else
				{
					$newExpiry = $_POST['newExpiryYear'] . "-" . $_POST['newExpiryMonth'] . "-" . $_POST['newExpiryDay'];
				}
				
				$success = saveCategory($_POST['row'], $_POST['newName'], $_POST['newDescription'], $_POST['newFlow'], $newActive, $newExpiry);
			}
	
            if ( $_POST['delete'] )
            {
                $sql = "DELETE FROM ledger_categories WHERE id = " . $_POST['row'];
                mysql_query($sql);
            }
            
            if ($success)
            {
            	displayValues(false, false);
            	echo "</table>";
            	displayButtons($displaySaveButton);
            }
        	else
        	{
	        	displayValues(true, $_POST['row']);
	        	displayErrorRowForCorrection($_POST['row'], $_POST['newName'], $_POST['newDescription'], $_POST['newFlow'], $newActive, $newExpiry);
	        	echo "</table>";
	        	displayButtons(true);
        	}
            
            function displayValues($skipEditRow, $row)
            {
				$sql = "SELECT id, name, description, flow, active, expiry
						FROM ledger_categories
						ORDER BY flow ASC, name ASC ";
				
				$result=mysql_query($sql);
				$counter = 0;
				
				echo "<table border=1>
						<tr>
							<th/>
							<th>Name</th>
							<th>Description</th>
							<th>Flow</th>
							<th>Active</th>
							<th>Expiry</th>
						</tr>";
						
				while($row = mysql_fetch_array($result))
				{
					echo "<tr>";
					
					if (!$skipEditRow || ($skipEditRow && $row == $row[id]))
					{
						if ( $_POST['edit'] && $_POST['row'] == $row['id'] )
						{
							echo "<td> <input type=radio name=\"" . "row" . "\" value = " . $row['id'] . " checked=\"checked\"> </td>";
							echo "<td> <input type=text id=\"name\" name=\"newName\" value = " . $row['name'] . " </td>";
							echo "<td> <input type=text id=\"description\" name=\"newDescription\" value = " . $row['description'] . " </td>";
							writeDirection("newFlow", $row['flow']);
							writeDateSelector("newActive", $row['active']);
							writeDateSelector("newExpiry", $row['expiry']);
						}
						else
						{	
							echo "<td> <input type=radio name=\"" . "row" . "\" value = " . $row['id'] . "> </td>";
							echo "<td>" . $row['name'] . "</td>";
							echo "<td>" . $row['description'] . "</td>";
							echo "<td>" . decodeDirection($row['flow']) . "</td>"; 
							echo "<td>" . date("d-M-Y", strtotime($row['active'])) . "</td>";
							
							if ($row['expiry'] == null )
							{
								echo "<td></td>";
							}
							else
							{
								echo "<td>" . date("d-M-Y", strtotime($row['expiry'])) . "</td>";
							}
						}
				  		
				  		echo "</tr>";
			  		}
				}
				
				if ($_POST['add'])
				{
					echo "<tr>";
					echo "<td> <input type=radio name=\"row\" value = \"NEW\" CHECKED/> </td>";
			  		echo "<td> <input type=\"text\" name=\"newName\" value=\"\" </td>";
			  		echo "<td> <input type=\"text\" name=\"newDescription\" value=\"\" </td>";
			  		writeDirection("newFlow", "I");
			  		writeDateSelector("newActive", date('m/d/Y h:i:s a', time())); //TODO load up the active variables
			  		writeDateSelector("newExpiry", null);
			  		echo "</tr>";
				}
			}
			
			function displayButtons($displaySaveButton)
			{
				if ( $displaySaveButton )
				{
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
				
				echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
				echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
				echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
			}
			
			function displayErrorRowForCorrection($row, $name, $description, $flow, $active, $expiry)
			{
				echo "<tr>";
				echo "<td> <input type=radio name=\"row\" value = \"" . $row . "\" CHECKED/> </td>";
			  	echo "<td> <input type=\"text\" name=\"newName\" value=\"" . $name . "\" </td>";
			  	echo "<td> <input type=\"text\" name=\"newDescription\" value=\"" . $description . "\" </td>";
			  	writeDirection("newFlow", $flow);
			  	writeDateSelector("newActive", $active);
			  	writeDateSelector("newExpiry", $expiry);
			}
						
			function saveCategory($row, $name, $description, $flow, $active, $expiry)
			{
				$success = validate($row, $name, $description, $flow, $active, $expiry); 
				
				if ($success)
				{
					if ( $row == "NEW" )
					{
						$exists = false;
					}
					else
					{
						$sql = "SELECT COUNT(*) FROM ledger_categories WHERE id = " . $row;
						$exists = mysql_query($sql);
					}
					
					if ( $exists )
					{
						$sql = "UPDATE ledger_categories SET
								name = '" . $name . "',
								description = '" . $description . "',
								flow = '" . codeDirection($flow) . "',
								active = '" . $active . "',";
								
						if ($expiry == null)
						{
							$sql = $sql . "expiry = null ";
						}
						else
						{
							$sql = $sql . "expiry = '" . $expiry . "' ";
						}
						
						$sql = $sql . "WHERE id = " . $row;
					}
					else
					{
						$sql = "INSERT INTO ledger_categories (name, description, flow, active, expiry) 
							VALUES ('" . $name . "', '" . $description . "', '". codeDirection($flow) . "', '" . $active . "', ";
						
						if ($expiry == null)
						{
							$sql = $sql . "null)";
						}
						else
						{
							$sql = $sql . "'" . $expiry . "')";
						}
					}
					mysql_query($sql);
				}
				else
				{
					echo( "Error: " . $_SESSION['errorMessage']);
				}
				
				return $success;
			}
			
			function validate ($row, $name, $description, $flow, $active, $expiry)
			{
				$success = true;
				
				if ($name == null)
				{
					$_SESSION['errorMessage'] = "Name must have a value.";
					$success = false;
				}
				
				if ($description == null)
				{
					$_SESSION['errorMessage'] = "Description must have a value.";
					$success = false;
				}
				
				if ($active == null)
				{
					$_SESSION['errorMessage'] = "Active must have a value.";
					$success = false;
				}
				
				return $success;
			}
			
			function decodeDirection($direction)
			{
				$fullDirection = "Income";
				
				if ($direction=="E")
				{
					$fullDirection = "Expense";
				}
				
				return $fullDirection;
			}
			
			function codeDirection($direction)
			{
				$codedDirection = "I";
				
				if ($direction == "Expense")
				{
					$codedDirection = "E";
				}
				
				return $codedDirection;
			}
			
			function writeDirection($fieldName, $defaultFlow)
			{
				echo "<td> <select name=\"" . $fieldName . "\" >";
				
				if ( $defaultFlow == "I" )
				{
					echo "<option selected=\"selected\">Income</option>";
				}
				else
				{
					echo"<option>Income</option>";
				}
				
				if ( $defaultFlow == "E" )
				{
					echo "<option selected=\"selected\">Expense</option>";
				}
				else
				{
					echo"<option>Expense</option>";
				}
				
				echo "</select>";
		  		echo "</td>";
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
			
			function isSelected( $writingMonth, $compareMonth )
			{
				if ( $writingMonth == $compareMonth )
				{
					return " SELECTED ";
				}
				else
				{ 
					return "";
				}
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