<?php include 'SecurityFunctions.php';
	include 'CommonFunctions.php';
	include 'Member.php';
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
	<TITLE>Maintain Members</TITLE>
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
				<li><a href="Thing1.php">Thing 1</a></li>
				<li><a href="Thing2.php">Thing 2</a></li>
				<li><a href="Thing3.php">Thing 3</a></li>
				<li><a href="Thing4.php">Thing 4</a></li>
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
		<h2>Active Members List</h2>
		<form action="MaintainMembers.php" method="post" enctype="multipart/form-data">
		
		<?php
			if ($_POST['add'])
			{
				displayAddForm();
				echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
				echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
			}
			elseif ($_POST['save'])
			{
				$member = new Member();
				$member->SetId($_POST['row']);
				$member->SetSwimSAId($_POST['newSwimSAId']);
				$member->SetSurname($_POST['newSurname']);
				$member->SetFamily($_POST['newFamily']);
				$member->SetGivenNames($_POST['newGivenNames']);
				$member->SetAddress1($_POST['newAddress1']);
				$member->SetAddress2($_POST['newAddress2']);
				$member->SetSuburb($_POST['newSuburb']);
				$member->SetState($_POST['newState']);
				$member->SetPostcode($_POST['newPostcode']);
				$member->SetGender($_POST['newGender']);
				$member->SetDateOfBirth($_POST['newDateOfBirth']);
				$member->SetJoinDate($_POST['newJoinDate']);
				$member->SetPhone($_POST['newPhone']);
				$member->SetMobilePhone($_POST['newMobilePhone']);
				$member->SetEmail($_POST['newEmail']);
				$member->SetEmergencyContact($_POST['newEmergencyContact']);
				$member->SetEmergencyContactNumber($_POST['newEmergencyContactNumber']);
				$member->SetMeetManagerId($_POST['newMeetManagerId']);
				$member->SetMemberType($_POST['newMembershipType']);
				$member->SetPaymentNomination($_POST['newPaymentNomination']);
				$member->SetCreated($_POST['newCreated']);
				$member->SetActive($_POST['newActive']);
				$member->SetExpiry($_POST['newExpiry']);
				
				if ($member->Save())
				{
					echo "Save successful.";
					displayActiveMembersList();
					echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
					echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
					echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
					echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
					echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
				}
				else
				{
					//displayEditForm($member);
					echo "<input type=\"submit\" name=\"cancel\" value=\"Cancel\">";
					echo "<input type=\"submit\" name=\"save\" value=\"Save\">";
				}
			}
			else
			{
				displayActiveMembersList();
				echo "<input type=\"submit\" name=\"show_details\" value=\"Show Details\">";
				echo "<input type=\"submit\" name=\"show_all\" value=\"Show All\">";
				echo "<input type=\"submit\" name=\"add\" value=\"Add\">";
				echo "<input type=\"submit\" name=\"edit\" value=\"Edit\">";
				echo "<input type=\"submit\" name=\"delete\" value=\"Delete\">";
			}
			
			function displayAddForm()
			{
				echo "<h3>Add New Member</h3>";
				echo "<table border=1>";
				
				echo "<tr>";
					echo "<td>SwimSA Id</td>";
					echo "<td><input id='newSwimSAId' type='text' name='newSwimSAId' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Surname</td>";
					echo "<td><input id='newSurname' type='text' name='newSurname' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Family</td>";
					echo "<td><input id='newFamily' type='text' name='newFamily' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Given Names</td>";
					echo "<td><input id='newGivenNames' type='text' name='newGivenNames' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Address 1</td>";
					echo "<td><input id='newAddress1' type='text' name='newAddress1' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Address 2</td>";
					echo "<td><input id='newAddress2' type='text' name='newAddress2' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Suburb</td>";
					echo "<td><input id='newSuburb' type='text' name='newSuburb' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>State</td>";
					writeStatesSelector("newState", "SA");
				echo "</tr>";
				echo "<tr>";
					echo "<td>Postcode</td>";
					echo "<td><input id='newPostcode' type='number' name='newPostcode' maxlength = '4' size = '4' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Gender</td>";
					writeGenderSelector("newGender", "F");
				echo "</tr>";
				echo "<tr>";
					echo "<td>Date of Birth</td>";
					echo "<td><input id='newDateOfBirth' name='newDateOfBirth' class='datepicker'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Join Date</td>";
					echo "<td><input id='newJoinDate' name='newJoinDate' class='datepicker'></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Phone</td>";
					echo "<td><input id='newPhone' type='text' name='newPhone' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Mobile Phone</td>";
					echo "<td><input id='newMobilePhone' type='text' name='newMobilePhone' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Email</td>";
					echo "<td><input id='newEmail' type='text' name='newEmail' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Emergency Contact Name</td>";
					echo "<td><input id='newEmergencyContact' type='text' name='newEmergencyContact' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Emergency Contact Number</td>";
					echo "<td><input id='newEmergencyContactNumber' type='text' name='newEmergencyContactNumber' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Meet Manager ID</td>";
					echo "<td><input id='newMeetManagerId' type='text' name='newMeetManagerId' value=''></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Membership Type</td>";
					writeMembershipTypeSelector("newMembershipType", "1");
				echo "</tr>";
				echo "<tr>";
					echo "<td>Payment Nomination</td>";
					writePaymentNominationSelector("newPaymentNomination", "A");
				echo "</tr>";
				echo "<tr>";
					echo "<td>Created</td>";
					echo "<td><input id='newCreated' name='newCreated' class='datepicker'></td>";
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
			
			function displayActiveMembersList()
			{
				$member = new Member();
				$members = $member->GetMembers(true);
				
				if (count($members > 0))
				{
					echo "<table border=1>
					<tr>
						<th/>
						<th>Surname</th>
						<th>Given Names</th>
						<th>Age</th>
						<th>Join Date</th>
						<th>Active</th>
					</tr>";
					
					foreach ($members as $thisMember)
					{
						echo "<tr>";
							echo "<td> <input type=radio name='row' value = '" . $thisMember->GetId() . "'</td>";
							echo "<td>" . $thisMember->GetSurname() . "</td>";
							echo "<td>" . $thisMember->GetGivenNames() . "</td>";
							echo "<td>" . $thisMember->GetAge() . "</td>";
							
							if ($thisMember->GetJoinDate() == null)
							{
								echo "<td>N/A</td>";
							}
							else
							{
								echo "<td>" . $thisMember->GetJoinDate() . "</td>";
							}
							
							if ($thisMember->GetActive() == null)
							{
								echo "<td>N/A</td>";
							}
							else
							{
								echo "<td>" . $thisMember->GetActive()->format("d-M-Y") . "</td>";
							}
						echo "</tr>";
					}
					
					echo "</table>";
				}
				else
				{
					echo "No members.<br>";
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