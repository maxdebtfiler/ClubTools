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
	<link rel="stylesheet" type="text/css" href="datepicker.css" /> 
	<script type="text/javascript" src="datepicker.js"></script>

	
	<!--DHTML menu-->
		
	<link href="editor_images/menu.css" rel="stylesheet" type="text/css" /><script type="text/javascript" src="inc/js/menu.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<script>
		function setFocusToTextBox(){
			document.getElementById("newName").focus();
		}
	</script>
	
	<TITLE>Maintain Fundraising Campaigns</TITLE>
</head>

<body onLoad="setFocusToTextBox();">
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
				<li><a href="MaintainFundraisingCampaigns.php">Maintain Fundraising Campaigns</a></li>
				<li><a href="xxx.php">Blank</a></li>
				<li><a href="xxx.php">Blank</a></li>
				<li><a href="xxx.php">Blank</a></li>
			</ul>
		</div>
		<div class="margin-news">
			<h2>News</h2>
			<p>
				<div id="NewsItem">
					<div id="NewsTitle"><a href="index.php?news&nid=1"><h1>Website created!...</h1></a></div>
					<div id="NewsDate"><a href="index.php?news&nid=1">08-05-2016</a></div>
					<div id="NewsOverview"><a href="index.php?news&nid=1">This website is still under construction, please visit us later!</a></div>
				</div>
				<div style="clear:both;"></div>
			</p>
		</div>
	</div>

	<div id="colTwo">
		<h2>Maintain Fundraising Campaigns</h2>
		<form action="MaintainFundraisingCampaigns.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['add'])
			{
				displayAddForm();
				echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
			}
			else if ($_POST['edit'])
			{
				if ($_POST['row'] > 0)
				{
					$campaignLoader = new FundraisingCampaign();
					$campaign = $campaignLoader->LoadFundraisingCampaign($_POST['row']);
					displayEditForm($campaign);
					echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
				else
				{
					echo "ERROR: No campaign selected.<br>";
					echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
				}
			}
			else if ($_POST['cancel'])
			{
				listCampaigns(false);
				echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
				echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
				echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
				echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
				echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
			}
			else if ($_POST['delete'])
			{
				$campaignLoader = new FundraisingCampaign();
				$campaign = $campaignLoader->LoadFundraisingCampaign($_POST['row']);
				
				if ($campaign->Delete())
				{
					echo "Campaign successfully deleted.";
					listCampaigns(false);
					echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
					echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
					echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
					echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
					echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
				}
				else
				{
					if ($campaign->IsError())
					{
						echo "Campaign delete failed.<br>";
					
						$errors = $campaign->GetErrorMessages();
						foreach ($errors as $message)
						{
							echo "ERROR: " . $message;
						}
					}
					
					listCampaigns(false);
					echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
					echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
					echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
					echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
					echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
				}
			}
			else if ($_POST['save'])
			{
				$campaign = new FundraisingCampaign();
				$campaign->SetId($_POST['row']);
				$campaign->SetName($_POST['newName']);
				$campaign->SetDescription($_POST['newDescription']);
				$campaign->SetTarget($_POST['newTarget']);
				$campaign->SetActive(date_create_from_format( "j-M-Y", $_POST['newActive']));
				$campaign->SetExpiry(date_create_from_format( "j-M-Y", $_POST['newExpiry']));
				
				if ($campaign->Save())
				{
					echo "Save successful.";
					listCampaigns(false);
					echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
					echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
					echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
					echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
					echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
				}
				else
				{
					displayEditForm($campaign);
					echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
			}
			else if ($_POST['show_details'])
			{
				if ($_POST['row'] > 0)
				{
					$campaignLoader = new FundraisingCampaign();
					$campaign = $campaignLoader->LoadFundraisingCampaign($_POST['row']);
					displaySummary($campaign);
					displayDetailedTransactions($campaign);
					echo "<input type=\"submit\" name=\"ok\" value=\"OK\">";
				}
				else
				{
					echo "ERROR: Please select a campaign before selecting this option.";
					listCampaigns(false);
					echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
					echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
					echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
					echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
					echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
				}
			}
			else if ($_POST['show_all'])
			{
				listCampaigns(true);
				echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
				echo "<input type=\"submit\" name=\"show_current\" value=\"Show Current\">";
				echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
				echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
				echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
			}
			else
			{
				listCampaigns(false);
				echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
				echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
				echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
				echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
				echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
			}
			
			function displaySummary($campaign)
			{
				echo "<h3>Fundraising Campaign Summary</h3>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td>" . $campaign->GetName() . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td>" . $campaign->GetDescription() . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Target</td>";
					echo "<td>" . money_format("%#1n", $campaign->GetTarget()) . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Total Raised</td>";
					echo "<td>" . money_format("%#1n", $campaign->GetTotalRaised()) . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Active Date</td>";
					echo "<td>" . $campaign->GetActive()->format("j-M-Y") . "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Expiry Date</td>";
					
					if ($campaign->GetExpiry() != null)
					{
						echo "<td>" . $campaign->GetExpiry()->format("j-M-Y") . "</td>";
					}
					
				echo "</tr>";
				echo "</table>";
			}
			
			function displayDetailedTransactions($campaign)
			{
				$transactions = $campaign->GetDetailedTransactions();
				
				if (count($transactions) == 0)
				{
					echo "No transactions<br>";
				}
				else
				{
					echo "<h3>Transctions</h3>";
					echo "<table border=1>";
					
					echo "<tr>";
						echo "<th>Date</th>";
						echo "<th>Type</th>";
						echo "<th>Name</th>";
						echo "<th>Description</th>";
						echo "<th>Amount</th>";
					echo "</tr>";
					
					foreach ($transactions as $t)
					{
						echo "<tr>";
						
						echo "<td>" . $t['date']->format("d-M-Y") . "</td>";
						
						if ($t['type'] == "I")
						{
							echo "<td>Income</td>";
						}
						else
						{
							echo "<td>Expense</td>";
						}
						
						echo "<td>" . $t['name'] . "</td>";
						echo "<td>" . $t['description'] . "</td>";
						echo "<td>" . money_format("%#1n", $t['amount']) . "</td>";
						
						echo "</tr>";
					}
					
					echo "</table>";
				}
			}
			
			function displayEditForm($campaign)
			{
				$errors = $campaign->GetErrorMessages();
				
				if ($campaign->IsError())
				{
					echo "<h3>Correct Fundraising Campaign Details</h3>";
					echo "<p>Errors:";
					foreach ($errors as $message)
					{
						echo "<br>" . $message;
					}
				}
				else
				{
					echo "<h3>Edit Fundraising Campaign</h3>";
				}
				
				echo "<input id='row' type='text' name='row' value='" . $campaign->GetId() . "'>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>" . redStarIfError("name", $campaign->GetErrorFields()) . "Name</td>";
					echo "<td><input id='newName' type='text' name='newName' value='" . $campaign->GetName() . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input id='newDescription' type='text' name='newDescription' value='" . $campaign->GetDescription() . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Target</td>";
					echo "<td><input id='newTarget' type='text' name='newTarget' value='" . $campaign->GetTarget() . "'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Active</td>";
					
					if ($campaign->GetActive() == null)
					{
						echo "<td><input id='newActive' name='newActive' class='datepicker'></td>";
					}
					else
					{
						echo "<td><input id='newActive' name='newActive' value='" . $campaign->GetActive()->format("j-M-Y") . "' class='datepicker'></td>";
					}
					
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Expiry</td>";
					
					if ($campaign->GetExpiry() == null)
					{
						echo "<td><input id='newExpiry' name='newExpiry' class='datepicker'></td>";
					}
					else
					{
						echo "<td><input id='newExpiry' name='newExpiry' value = '" . $campaign->GetExpiry()->format("j-M-Y") . "' class='datepicker'></td>";
					}
					
				echo "</tr>";
				echo "</table>";
			}
			
			function displayAddForm()
			{
				echo "<h3>Add Fundraising Campaign</h3>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>Name</td>";
					echo "<td><input id=\"newName\" type=\"text\" name=\"newName\" value=\"\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Description</td>";
					echo "<td><input id=\"newDescription\" type=\"text\" name=\"newDescription\" value=\"\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Target</td>";
					echo "<td><input id=\"newTarget\" type=\"text\" name=\"newTarget\" value=\"\"></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Active</td>";
					echo "<td><input id='newActive' name='newActive' class='datepicker'></td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>Expiry</td>";
					echo "<td><input id='newExpiry' name='newExpiry' class='datepicker'></td>";
				echo "</tr>";
				
				echo "</table>";
			}
			
			function listCampaigns($all)
			{
				$camp = new FundraisingCampaign();
				
				if ($all)
				{
					$currentCampaigns = $camp->GetCampaigns(null);
				}
				else
				{
					$today = new DateTime();
					$currentCampaigns = $camp->GetCampaigns($today);
				}
				
				if (count($currentCampaigns) > 0)
				{
					echo "<table border=1>
					<tr>
						<th/>
						<th>Campaign Name</th>
						<th>Description</th>
						<th>Target</th>
						<th>Raised</th>
						<th>Active</th>
						<th>Expiry</th>
					</tr>";
					
					foreach ($currentCampaigns as $campaign)
					{
						echo "<tr>";
							echo "<td> <input type=radio name='row' value = '" . $campaign->GetId() . "'</td>";
							echo "<td>" . $campaign->GetName() . "</td>";
							echo "<td>" . $campaign->GetDescription() . "</td>";
							echo "<td>" . money_format("%.0n", $campaign->GetTarget()) . "</td>";
							echo "<td>" . money_format("%.0n", $campaign->GetTotalRaised()) . "</td>";
							echo "<td>" . date("d-M-Y", strtotime($campaign->GetActive())) . "</td>";
							
							if ($campaign->GetExpiry() == null)
							{
								echo "<td></td>";
							}
							else
							{
								echo "<td>" . date("d-M-Y", strtotime($campaign->GetExpiry())) . "</td>";
							}	
						echo "</tr>";
					}
					
					echo "</table>";
				}
				else
				{
					echo "No campaigns.<br>";
				}
				
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
		<p>Copyright (c)2016 Artistan Solutions</p>
	</div>

	<script type="text/javascript"></script> 
</body>

</html>