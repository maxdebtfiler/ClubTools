<?php
	class Member
	{
		private $id = 0;
		private $swimSaId = 0;
		private $surname = null;
		private $family = null;
		private $givenNames = null;
		private $address1 = null;
		private $address2 = null;
		private $addressSuburb = null;
		private $addressState = null;
		private $addressPostcode = 0;
		private $gender = null;
		private $dateOfBirth = null;
		private $joinDate = null;
		private $phone = null;
		private $mobilePhone = null;
		private $email = null;
		private $emergencyContact = null;
		private $emergencyContactNumber = null;
		private $meetManagerId = null;
		private $memberType = 0;
		private $paymentNomination = 0;
		private $created = null;
		private $active = null;
		private $expiry = null;
		
		private $hasError = false;
		private $errorMessages = array();
		private $errorFields = array();
		
		function __construct()
		{	
		}
		
		function LoadMemberBySwimSAId($swimSAId)
		{
			$sql = "SELECT id FROM members WHERE swimsa_memberid = '" . $swimSAId . "'";
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row[0] > 0)
			{
				$id = $row[0];
				$this->LoadMember($id);
			}
		}
		
		function LoadMember($id)
		{
			$sql = "SELECT swimsa_memberid, surname, family, given_names, address1, address2, suburb, state, postcode, gender, date_of_birth, join_date, phone_home, phone_mobile, email, 
				emergency_contact_person, emergency_contact_number, meet_manager_id, membership_type, payment_nomination, created, active, expiry 
				FROM members 
				WHERE id = " . $id;
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			$row = mysqli_fetch_array($result);
			
			if ($row != null)
			{
				$this->id = $id;
				$this->swimSaId = $row['swimsa_memberid'];
				$this->surname = $row['surname'];
				$this->family = $row['family'];
				$this->givenNames = $row['given_names'];
				$this->address1 = $row['address1'];
				$this->address2 = $row['address2'];
				$this->addressSuburb = $row['suburb'];
				$this->addressState = $row['state'];
				$this->addressPostcode = $row['postcode'];
				$this->gender = $row['gender'];
				$this->dateOfBirth = date_create_from_format("Y-m-d", $row['date_of_birth']);
				$this->joinDate = date_create_from_format("Y-m-d", $row['join_date']);
				$this->phone = $row['phone_home'];
				$this->mobilePhone = $row['phone_mobile'];
				$this->email = $row['email'];
				$this->emergencyContact = $row['emergency_contact_person'];
				$this->emergencyContactNumber = $row['emergency_contact_number'];
				$this->meetManagerId = $row['meet_manager_id'];
				$this->memberType = $row['membership_type'];
				$this->paymentNomination = $row['payment_nomination'];
				$this->created = date_create_from_format("Y-m-d", $row['created']);
				
				if ($row['active'] != null )
				{
					$this->active = new DateTime($row['active']);
				}
				
				if ($row['expiry'] != null)
				{
					$this->expiry = new DateTime($row['expiry']);
				}
			}
		}
		
		function GetMembers($active)
		{
			$members = array();
			
			$sql = "SELECT id, swimsa_memberid, surname, family, given_names, address1, address2, suburb, state, postcode, gender, date_of_birth, join_date, phone_home, phone_mobile, email, 
				emergency_contact_person, emergency_contact_number, meet_manager_id, membership_type, payment_nomination, created, active, expiry FROM members ";
				
			if ($active)
			{
				$sql = $sql . " WHERE expiry is null OR expiry >= DATE(NOW())";
			}				
				
			$sql = $sql . " ORDER BY surname ASC, family ASC, date_of_birth ASC";
			
			$result = mysqli_query($_SESSION['databaseConnection'], $sql);
			
			while ($row = mysqli_fetch_array($result))
			{
				$member = new Member();
				$member->SetId($row['id']);
				$member->SetSwimSAId($row['swimsa_memberid']);
				$member->SetSurname($row['surname']);
				$member->SetFamily($row['family']);
				$member->SetGivenNames($row['given_names']);
				$member->SetAddress1($row['address1']);
				$member->SetAddress2($row['address2']);
				$member->SetSuburb($row['suburb']);
				$member->SetState($row['state']);
				$member->SetPostcode($row['postcode']);
				$member->SetGender($row['gender']);
				$member->SetDateOfBirth(date_create_from_format("Y-m-d", $row['date_of_birth']));
				$member->SetJoinDate(date_create_from_format("Y-m-d", $row['join_date']));
				$member->SetPhone($row['phone_home']);
				$member->SetMobilePhone($row['phone_mobile']);
				$member->SetEmail($row['email']);
				$member->SetEmergencyContact($row['emergency_contact_person']);
				$member->SetEmergencyContactNumber($row['emergency_contact_number']);
				$member->SetMeetManagerId($row['meet_manager_id']);
				$member->SetMemberType($row['membership_type']);
				$member->SetPaymentNomination($row['payment_nomination']);
				$member->SetCreated(date_create_from_format("Y-m-d", $row['created']));
				
				if ($row['active'] != null )
				{
					$member->active = new DateTime($row['active']);
				}
				
				if ($row['expiry'] != null)
				{
					$member->expiry = new DateTime($row['expiry']);
				}
				
				array_push($members, $member);
			}
			
			return $members;
		}
		
		function Save()
		{
			$this->Validate();
			
			if ($this->exists())
			{
				$sql = "UPDATE members ";
				
				if ($this->swimSaId == null)
				{
					$sql = $sql . "SET swimsa_memberid = null, ";
				}
				else
				{
					$sql = $sql . "SET swimsa_memberid = '" . $this->swimSaId . "', ";
				}
				
				if ($this->surname == null)
				{
					$sql = $sql . "surname = null, ";
				}
				else
				{
					$sql = $sql . "surname = '" . $this->surname . "', ";
				}
				
				if ($this->givenNames == null)
				{
					$sql = $sql . "given_names = null, ";
				}
				else
				{
					$sql = $sql . "given_names = '" . $this->givenNames . "', ";
				}
				
				if ($this->address1 == null)
				{
					$sql = $sql . "address1 = null, ";
				}
				else
				{
					$sql = $sql . "address1 = '" . $this->address1 . "', ";
				}
				
				if ($this->address2 == null)
				{
					$sql = $sql . "address2 = null, ";
				}
				else
				{
					$sql = $sql . "address2 = '" . $this->address2 . "', ";
				}
				
				if ($this->addressSuburb == null)
				{
					$sql = $sql . "suburb = null, ";
				}
				else
				{
					$sql = $sql . "suburb = '" . $this->addressSuburb . "', ";
				}
				
				if ($this->addressState == null)
				{
					$sql = $sql . "state = null, ";
				}
				else
				{
					$sql = $sql . "state = '" . $this->addressState . "', ";
				}
				
				if ($this->addressPostcode == null)
				{
					$sql = $sql . "postcode = null, ";
				}
				else
				{
					$sql = $sql . "postcode = " . $this->addressPostcode . ", ";
				}
				
				if ($this->gender == null)
				{
					$sql = $sql . "gender = null, ";
				}
				else
				{
					$sql = $sql . "gender = '" . $this->gender . "', ";
				}
				
				if ($this->dateOfBirth == null)
				{
					$sql = $sql . "date_of_birth = null, ";
				}
				else
				{
					$sql = $sql . "date_of_birth = '" . $this->dateOfBirth->format("Y-m-d") . "', ";
				}
				
				if ($this->phone == null)
				{
					$sql = $sql . "phone_home = null, ";
				}
				else
				{
					$sql = $sql . "phone_home = '" . $this->phone . "', ";
				}
				
				if ($this->mobilePhone == null)
				{
					$sql = $sql . "phone_mobile = null, ";
				}
				else
				{
					$sql = $sql . "phone_mobile = '" . $this->mobilePhone . "', ";
				}
				
				if ($this->joinDate == null)
				{
					$sql = $sql . "join_date = null, ";
				}
				else
				{
					$sql = $sql . "join_date = '" . $this->joinDate->format("Y-m-d") . "', ";
				}
				
				if ($this->email == null)
				{
					$sql = $sql . "email = null, ";
				}
				else
				{
					$sql = $sql . "email = '" . $this->email . "', ";
				}
				
				if ($this->emergencyContact == null)
				{
					$sql = $sql . "emergency_contact_person = null, ";
				}
				else
				{
					$sql = $sql . "emergency_contact_person = '" . $this->emergencyContact . "', ";
				}
				
				if ($this->emergencyContactNumber == null)
				{
					$sql = $sql . "emergency_contact_number = null, ";
				}
				else
				{
					$sql = $sql . "emergency_contact_number = '" . $this->emergencyContactNumber . "', ";
				}
				
				if ($this->meetManagerId == null)
				{
					$sql = $sql . "meet_manager_id = null, ";
				}
				else
				{
					$sql = $sql . "meet_manager_id = '" . $this->meetManagerId . "', ";
				}
				
				if ($this->memberType == null)
				{
					$sql = $sql . "membership_type = null, ";
				}
				else
				{
					$sql = $sql . "membership_type = " . $this->memberType . ", ";
				}
				
				if ($this->paymentNomination == null)
				{
					$sql = $sql . "payment_nomination = null, ";
				}
				else
				{
					$sql = $sql . "payment_nomination = '" . $this->paymentNomination . "', ";
				}
				
				if ($this->created == null)
				{
					$sql = $sql . "created = null, ";
				}
				else
				{
					$sql = $sql . "created = '" . $this->created->format("Y-m-d H:i:s") . "', ";
				}
				
				if ($this->active == null)
				{
					$sql = $sql . "active = null, ";
				}
				else
				{
					$sql = $sql . "active = '" . $this->active->format("Y-m-d") . "', ";
				}
				
				if ($this->expiry == null)
				{
					$sql = $sql . "expiry = null ";
				}
				else
				{
					$sql = $sql . "expiry = '" . $this->expiry->format("Y-m-d") . "' ";
				}
				
				$sql = $sql . "WHERE id = " . $this->id;
				
				if(!mysqli_query($_SESSION['databaseConnection'], $sql))
				{
					$this->hasError = true;
					array_push($this->errorMessages, "<br>Database sql error: " . $sql);
				}
			}
			else
			{
				$sql = "INSERT INTO members (swimsa_memberid, surname, family, given_names, address1, address2, suburb, state, postcode, gender, date_of_birth, phone_home, phone_mobile,
					join_date, email, emergency_contact_person, emergency_contact_number, meet_manager_id, membership_type, payment_nomination, created, active, expiry) VALUES (";

				if ($this->swimSaId == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->swimSaId . "', ";
				}
				
				if ($this->surname == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->surname . "', ";
				}
				
				if ($this->family == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->family . "', ";
				}
				
				if ($this->givenNames == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->givenNames . "', ";
				}
				
				if ($this->address1 == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->address1 . "', ";
				}
				
				if ($this->address2 == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->address2 . "', ";
				}
				
				if ($this->addressSuburb == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->addressSuburb . "', ";
				}
				
				if ($this->addressState == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->addressState . "', ";
				}
				
				if ($this->addressPostcode == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . $this->addressPostcode . ", ";
				}
				
				if ($this->gender == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->gender . "', ";
				}
				
				if ($this->dateOfBirth == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->dateOfBirth->format("Y-m-d") . "', ";
				}
				
				if ($this->phone == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->phone . "', ";
				}
				
				if ($this->mobilePhone == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->mobilePhone . "', ";
				}
				
				if ($this->joinDate == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->joinDate->format("Y-m-d") . "', ";
				}
				
				if ($this->email == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->email . "', ";
				}
				
				if ($this->emergencyContact == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->emergencyContact . "', ";
				}
				
				if ($this->emergencyContactNumber == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->emergencyContactNumber . "', ";
				}
				
				if ($this->meetManagerId = null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->meetManagerId . "', ";
				}
				
				if ($this->memberType == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . $this->memberType . ", ";
				}
				
				if ($this->paymentNomination == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->paymentNomination . "', ";
				}
				
				if ($this->created == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->created->format("Y-m-d H:i:s") . "', ";
				}
				
				if ($this->active == null)
				{
					$sql = $sql . "null, ";
				}
				else
				{
					$sql = $sql . "'" . $this->active->format("Y-m-d") . "', ";
				}
				
				if ($this->expiry == null)
				{
					$sql = $sql . "null)";
				}
				else
				{
					$sql = $sql . "'" . $this->expiry->format("Y-m-d") . "')";
				}
				
				if(!mysqli_query($_SESSION['databaseConnection'], $sql))
				{
					$this->hasError = true;
					array_push($this->errorMessages, "<br>Database sql error: " . $sql);
				}
			}
		}
		
		function DeleteMember($id)
		{
			$sql = "DELETE FROM members WHERE id = " . $id;
			!mysqli_query($_SESSION['databaseConnection'], $sql);
		}
		
		function Delete()
		{
			$sql = "DELETE FROM members WHERE id = " . $this->id;
			!mysqli_query($_SESSION['databaseConnection'], $sql);
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
			
			if ($this->surname == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Surname is required.");
				array_push($this->errorFields, "surname");
			}
			
			if ($this->givenNames == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Given names are required.");
				array_push($this->errorFields, "givenNames");
			}
			
			if ($this->gender == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Gender is required.");
				array_push($this->errorFields, "gender");
			}
			
			if ($this->gender == null)
			{
				$this->hasError = true;
				array_push($this->errorMessages, "Gender is required.");
				array_push($this->errorFields, "gender");
			}
			
			return !$this->hasError;
		}
		
		private function exists()
		{
			$exists = false;
			
			if ($this->id > 0)
			{
				$sql = "SELECT COUNT(*) FROM members WHERE id = " . $this->id;
				$result = mysqli_query($_SESSION['databaseConnection'], $sql);
				$row = mysqli_fetch_array($result);
				
				if ($row[0] > 0)
				{
					$exists = true;
				}
			}
			
			return $exists;
		}
		
		private function clearError()
		{
			$this->hasError = false;
			reset($this->errorMessages);
			reset($this->errorFields);
		}
		
		function GetId() {return $this->id;}
		function GetSwimSAId() {return $this->swimSaId;}
		function GetSurname() {return $this->surname;}
		function GetFamily() {return $this->family;}
		function GetGivenNames() {return $this->givenNames;}
		function GetAddress1() {return $this->address1;}
		function GetAddress2() {return $this->address2;}
		function GetSuburb() {return $this->addressSuburb;}
		function GetState() {return $this->addressState;}
		function GetPostcode() {return $this->addressPostcode;}
		function GetGender() {return $this->gender;}
		function GetDateOfBirth() {return $this->dateOfBirth;}
		function GetAge() {return $this->yearsBetween(new DateTime("now"), $this->dateOfBirth);}
		function GetJoinDate() {return $this->joinDate;}
		function GetPhone() {return $this->phone;}
		function GetMobilePhone() {return $this->mobilePhone;}
		function GetEmail() {return $this->email;}
		function GetEmergencyContact() {return $this->emergencyContact;}
		function GetEmergencyContactNumber() {return $this->emergencyContactNumber;}
		function GetMeetManagerId() {return $this->meetManagerId;}
		function GetMemberType() {return $this->memberType;}
		function GetPaymentNomination() {return $this->paymentNomination;}
		function GetCreated() {return $this->created;}
		function GetActive() {return $this->active;}
		function GetExpiry() {return $this->expiry;}
		function IsError() {return $this->hasError;}
		function GetErrorMessages() {return $this->errorMessages;}
		function GetErrorFields() {return $this->errorFields;}
		
		function SetId($id) {$this->id = $id;}
		function SetSwimSAId($swimSaId) {$this->swimSaId = $swimSaId;}
		function SetSurname($surname) {$this->surname = $surname;}
		function SetFamily($family) {$this->family = $family;}
		function SetGivenNames($givenNames) {$this->givenNames = $givenNames;}
		function SetAddress1($address1) {$this->address1 = $address1;}
		function SetAddress2($address2) {$this->address2 = $address2;}
		function SetSuburb($suburb) {$this->addressSuburb = $suburb;}
		function SetState($state) {$this->addressState = $state;}
		function SetPostcode($postcode) {$this->addressPostcode = $postcode;}
		function SetGender($gender) {$this->gender = $gender;}
		function SetDateOfBirth($dateOfBirth) {$this->dateOfBirth = $dateOfBirth;}
		function SetJoinDate($joinDate) {$this->joinDate = $joinDate;}
		function SetPhone($phone) {$this->phone = $phone;}
		function SetMobilePhone($mobilePhone) {$this->mobilePhone = $mobilePhone;}
		function SetEmail($email) {$this->email = $email;}
		function SetEmergencyContact($emergencyContact) {$this->emergencyContact = $emergencyContact;}
		function SetEmergencyContactNumber($emergencyContactNumber) {$this->emergencyContactNumber = $emergencyContactNumber;}
		function SetMeetManagerId($meetManagerId) {$this->meetManagerId = $meetManagerId;}
		function SetMemberType($memberType) {$this->memberType = $memberType;}
		function SetPaymentNomination($paymentNomination) {$this->paymentNomination = $paymentNomination;}
		function SetCreated($created) {$this->created = $created;}
		function SetActive($active) {$this->active = $active;}
		function SetExpiry($expiry) {$this->expiry = $expiry;}
		
		private function yearsBetween($date1, $date2)
		{
			$age = 0;
			
			if ($date2 != null)
			{
				list($year1, $dayOfYear1) = explode(' ', $date1->format('Y z'));
				list($year2, $dayOfYear2) = explode(' ', $date2->format('Y z'));
				$age = $year1 - $year2 - ($dayOfYear1 < $dayOfYear2);
			}
			
			return $age;
		}
	}
?>