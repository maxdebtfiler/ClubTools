<?php
	include 'CommonFunctions.php';
	include 'Member.php';
	session_start();
	date_default_timezone_set('Australia/Adelaide');
	
	init();
	$_SESSION['databaseConnection'] = mysqli_connect("localhost", "onkaswim_toolusr", "Mclarenf1", "onkaswim_clubtoolstest") or die(mysql_error());
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+USING THE onkaswim_clubtoolstest database+";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	echo "<br>+Test Getters and Setters+";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	$m = new Member();
	$m->SetId(54);
	$m->SetSwimSAId(665456);
	$m->SetSurname("Smith");
	$m->SetFamily("Brown");
	$m->SetGivenNames("Jemima Sally");
	$m->SetAddress1("Level 3");
	$m->SetAddress2("11 Rusty Rd");
	$m->SetSuburb("Tilted Towers");
	$m->SetState("NSW");
	$m->SetPostcode(5455);
	$m->SetGender("M");
	$m->SetDateOfBirth(date_create_from_format("Y-m-d", "2005-7-15"));
	$m->SetJoinDate(date_create_from_format("Y-m-d", "2015-6-19"));
	$m->SetPhone("083911990");
	$m->SetMobilePhone("0414600249");
	$m->SetEmail("bob.brown@elongitude.com");
	$m->SetEmergencyContact("My Mum");
	$m->SetEmergencyContactNumber("0407853016");
	$m->SetMemberType(2);
	$m->SetPaymentNomination(1);
	$m->SetActive(date_create_from_format("Y-m-d", "2017-10-01"));
	$m->SetExpiry(date_create_from_format("Y-m-d", "2017-9-30"));
	
	echo "<br>ID (should be 54): " . $m->GetId();
	echo "<br>SwimSA ID (should be 665456): " . $m->GetSwimSAId();
	echo "<br>Surname (should be Smith): " . $m->GetSurname();
	echo "<br>Given Names (should be Jemima Sally): " . $m->GetGivenNames();
	echo "<br>Address 1 (should be Level 3): " . $m->GetAddress1();
	echo "<br>Address 2 (should be 11 Rusty Rd): " . $m->GetAddress2();
	echo "<br>Suburb (should be Tilted Towers): " . $m->GetSuburb();
	echo "<br>State (should be NSW): " . $m->GetState();
	echo "<br>Postcode (should be 5455): " . $m->GetPostcode();
	echo "<br>Gender (should be M): " . $m->GetGender();
	echo "<br>DOB (should be 2005-7-15): " . $m->GetDateOfBirth()->format("d-m-Y");
	echo "<br>Age (should be 12): " . $m->GetAge();
	echo "<br>Join Date (should be 2015-6-19): " . $m->GetJoinDate()->format("d-m-Y");
	echo "<br>Phone (should be 083911990): " . $m->GetPhone();
	echo "<br>Mobile Phone (should be 0414600249): " . $m->GetMobilePhone();
	echo "<br>Email (should be bob.brown@elongitude.com): " . $m->GetEmail();
	echo "<br>Emergency Contact (should be My Mum): " . $m->GetEmergencyContact();
	echo "<br>SetEmergencyContactNumber (should be 0407853016): " . $m->GetEmergencyContactNumber();
	echo "<br>Member Type (should be 2): " . $m->GetmemberType();
	echo "<br>Payment Nomination (should be 1): " . $m->GetPaymentNomination();
	echo "<br>Active (should be 2017-10-01): " . $m->GetActive()->format("d-m-Y");
	echo "<br>Expiry (should be 2017-9-30): " . $m->GetExpiry()->format("d-m-Y");
	
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+Load Members+";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	$members = $m->GetMembers(false);
	echo "<br>Count of members:" . sizeof($members);
	
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+Load Individual Member - Sophie Barrow";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	$m = new Member();
	$m->LoadMember(82);
	
	echo "<br>ID (should be 82: " . $m->GetId();
	echo "<br>SwimSA ID: " . $m->GetSwimSAId();
	echo "<br>Surname: " . $m->GetSurname();
	echo "<br>Given Names: " . $m->GetGivenNames();
	echo "<br>Address 1: " . $m->GetAddress1();
	echo "<br>Address 2: " . $m->GetAddress2();
	echo "<br>Suburb: " . $m->GetSuburb();
	echo "<br>State: " . $m->GetState();
	echo "<br>Postcode: " . $m->GetPostcode();
	echo "<br>Gender: " . $m->GetGender();
	if ($m->GetDateOfBirth() == null)
	{
		echo "<br>DOB: null";
	}
	else
	{
		var_dump($m);
		echo "<br>DOB: " . $m->GetDateOfBirth()->format("d-m-Y");
		echo "<br>Age: " . $m->GetAge();
	}	
	if ($m->GetJoinDate() == null)
	{
		echo "<br>Join Date: null";
	}
	else
	{
		echo "<br>Join Date: " . $m->GetJoinDate()->format("d-m-Y");
	}
	echo "<br>Phone: " . $m->GetPhone();
	echo "<br>Mobile Phone: " . $m->GetMobilePhone();
	echo "<br>Email: " . $m->GetEmail();
	echo "<br>Emergency Contact: " . $m->GetEmergencyContact();
	echo "<br>SetEmergencyContactNumber: " . $m->GetEmergencyContactNumber();
	echo "<br>Member Type: " . $m->GetmemberType();
	echo "<br>Payment Nomination: " . $m->GetPaymentNomination();
	echo "<br>Created: " . $m->GetCreated();
	if ($m->GetActive() == null)
	{
		echo "<br>Active: null";
	}
	else
	{
		echo "<br>Active: " . $m->GetActive()->format("d-m-Y");
	}
	if ($m->GetExpiry() == null)
	{
		echo "<br>Expiry: null";
	}
	else
	{
		echo "<br>Expiry: " . $m->GetExpiry()->format("d-m-Y");
	}
	
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+Save Updates to Individual Member - Sophie Barrow";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	$m = new Member();
	$m->LoadMember(82);
	
	$swimSAId = $m->GetSwimSAId();
	$m->SetSwimSAId("12345");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>SwimSAId (should be 12345): " . $m->GetSwimSAId();
	$m->SetSwimSAId($swimSAId);
	$m->Save();
	
	$surname = $m->GetSurname();
	$m->SetSurname("Duck");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Surname (should be Duck): " . $m->GetSurname();
	$m->SetSurname($surname);
	$m->Save();
	
	$given = $m->GetGivenNames();
	$m->SetGivenNames("Daffy");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Given Names (should be Daffy): " . $m->GetGivenNames();
	$m->SetGivenNames($given);
	$m->Save();
	
	$address1 = $m->GetAddress1();
	$m->SetAddress1("11 Ferguson Court");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Address1 (should be 11 Ferguson Court): " . $m->GetAddress1();
	$m->SetAddress1($address1);
	$m->Save();
	
	$address2 = $m->GetAddress2();
	$m->SetAddress2("11 Ferguson Court");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Address2 (should be 11 Ferguson Court): " . $m->GetAddress2();
	$m->SetAddress2($address2);
	$m->Save();
	
	$suburb = $m->GetSuburb();
	$m->SetSuburb("MOUNT BARKER");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Suburb (should be MOUNT BARKER): " . $m->GetSuburb();
	$m->SetSuburb($suburb);
	$m->Save();
	
	$state = $m->GetState();
	$m->SetState("SA");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>State (should be SA): " . $m->GetState();
	$m->SetState($state);
	$m->Save();
	
	$postcode = $m->GetPostcode();
	$m->SetPostcode(5251);
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Postcode (should be 5251): " . $m->GetPostcode();
	$m->SetPostcode($postcode);
	$m->Save();
	
	$gender = $m->GetGender();
	$m->SetGender("F");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Gender (should be F): " . $m->GetGender();
	$m->SetGender($gender);
	$m->Save();
	
	$dob = $m->GetDateOfBirth();
	$m->SetDateOfBirth(date_create_from_format("Y-m-d", "2003-5-16"));
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Date of Birth (should be 16th May 2003): " . $m->GetDateOfBirth()->format("d-m-Y");
	$m->SetDateOfBirth($dob);
	$m->Save();
	
	$join = $m->GetJoinDate();
	$m->SetJoinDate(date_create_from_format("Y-m-d", "2015-1-16"));
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Join Date (should be 16th Jan 2015): " . $m->GetJoinDate()->format("d-m-Y");
	$m->SetJoinDate($join);
	$m->Save();
	
	$phone = $m->GetPhone();
	$m->SetPhone("+61 414 600 249");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Phone (should be +61 414 600 249): " . $m->GetPhone();
	$m->SetPhone($phone);
	$m->Save();
	
	$mobile = $m->GetMobilePhone();
	$m->SetMobilePhone("+61 414 600 249");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Mobile Phone (should be +61 414 600 249): " . $m->GetMobilePhone();
	$m->SetMobilePhone($mobile);
	$m->Save();
	
	$email = $m->GetEmail();
	$m->SetEmail("maxdebtfiler@gmail.com");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Email (should be maxdebtfiler@gmail.com): " . $m->GetEmail();
	$m->SetEmail($email);
	$m->Save();
	
	$emergency = $m->GetEmergencyContact();
	$m->SetEmergencyContact("Jesus");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Emergency Contact (should be Jesus): " . $m->GetEmergencyContact();
	$m->SetEmergencyContact($emergency);
	$m->Save();
	
	$emergencyNum = $m->GetEmergencyContactNumber();
	$m->SetEmergencyContactNumber("+61407885695");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Emergency Contact Number (should be +61407885695): " . $m->GetEmergencyContactNumber();
	$m->SetEmergencyContactNumber($emergencyNum);
	$m->Save();
	
	$type = $m->GetMemberType();
	$m->SetMemberType(3);
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Membership Type (should be 3): " . $m->GetMemberType();
	$m->SetMemberType($type);
	$m->Save();
	
	$nomination = $m->GetPaymentNomination();
	$m->SetPaymentNomination("Y");
	$m->Save();
	$m->LoadMember(82);
	echo "<br>Email (should be Y): " . $m->GetPaymentNomination();
	$m->SetPaymentNomination($nomination);
	$m->Save();
	
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+Save New Member - Charlotte Cooke";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	$m = new Member();
	$m->SetSwimSAId("123456");
	$m->SetSurname("Cooke");
	//$m->SetFamily("Cooke");
	$m->SetGivenNames("Charlotte");
	$m->SetAddress1("11 Ferguson Court");
	$m->SetAddress2("11 Ferguson Court");
	$m->SetSuburb("MOUNT BARKER");
	$m->SetState("SA");
	$m->SetPostcode(5251);
	$m->SetGender("F");
	$m->SetDateOfBirth(date_create_from_format("Y-m-d", "2006-2-16"));
	$m->SetJoinDate(date_create_from_format("Y-m-d", "2018-1-16"));
	$m->SetPhone("+61 414 600 249");
	$m->SetMobilePhone("+61 414 600 249");
	$m->SetEmail("maxdebtfiler@gmail.com");
	$m->SetEmergencyContact("Jesus");
	$m->SetEmergencyContactNumber("+61407885695");
	$m->SetMemberType(2);
	$m->SetPaymentNomination("Y");
	$m->SetActive(date_create_from_format("Y-m-d", "2003-5-16"));
	$m->Save();
	$m->LoadMemberBySwimSAId("123456");
	echo "<br>Member Created ID (should be id):" . $m->GetId();
		
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+Age Calcs";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>Date of Birth: " . $m->GetDateOfBirth()->format("d-m-Y");
	echo "<br>Age: " . $m->GetAge();
	
	$m->Delete();
?>