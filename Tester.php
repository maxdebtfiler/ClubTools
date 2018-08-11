<?php
	include 'CommonFunctions.php';
	include 'FundraisingCampaign.php';
	include 'TrainingFeePayment.php';
	include 'IncomeItem.php';
	session_start();
	date_default_timezone_set('Australia/Adelaide');
	
	init();
	$_SESSION['databaseConnection'] = mysqli_connect("localhost", "onkaswim_toolusr", "Mclarenf1", "onkaswim_clubtoolstest") or die(mysql_error());
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+USING THE onkaswim_clubtoolstest database+";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	
	/*
	echo "*********************";
	echo "<br>Test Followup Item";
	echo "<br>*********************";
	echo "<br>Test FollowupItem - Sets and Gets";
	echo "<br>*********************";
	$fu = new FollowupItem();
	$fu->SetId(15);
	$fu->SetIncomeItemId(102);
	$d = date_create_from_format("Y-m-d", "2016-7-15");
	$fu->SetFollowupDate($d);
	$fu->SetFollowupReason("This is the followup reason.");
	$fu->SetFollowupAction("This is the followup action.");
	
	echo "<br>Id (should be 15):" . $fu->GetId();
	echo "<br>IncomeId (should be 102:" . $fu->GetIncomeItemId();
	$da = $fu->GetFollowupDate();
	echo "<br>Followup Date (should be 15-7-16):" . $da->format("d-m-Y");
	echo "<br>Reason:" . $fu->GetFollowupReason();
	echo "<br>Action:" . $fu->GetFollowupAction();
	
	echo "<br>*********************";
	echo "<br>Test FollowupItem - Validation";
	echo "<br>*********************";
	
	$f = new FollowupItem();
	$f->SetId(-1);
	$f->Validate();
	$messages = $f->GetErrorMessages();
	
	foreach ($messages as $m)
	{
		echo "<br>Message: " . $m;
	}
	
	echo "<br>*********************";
	echo "<br>Test FollowupItem - Save Insert";
	echo "<br>*********************";
	
	$fu = new FollowupItem();
	$fu->SetIncomeItemId(102);
	$d = date_create_from_format("Y-m-d", "2016-7-15");
	$fu->SetFollowupDate($d);
	$fu->SetFollowupReason("This is the followup reason.");
	$fu->SetFollowupAction("This is the followup action.");
	$fu->Save();
	
	$items = $fu->GetFollowupItemsForIncomeId(102);
	foreach ($items as $it)
	{
		echo "<br>Id:" . $it->GetId();
		echo "<br>IncomeId:" . $it->GetIncomeItemId();
		$da = $it->GetFollowupDate();
		echo "<br>Date:" . $da->format("d-m-Y");
		echo "<br>Reason:" . $it->GetFollowupReason();
		echo "<br>Action:" . $it->GetFollowupAction();
	}
	*/
	
	/*
	echo "<br>*********************";
	echo "<br>Test FollowupItem - Save Update";
	echo "<br>*********************";
	
	$fu = new FollowupItem();
	$fu->LoadFollowupItem(10);
	$fu->SetFollowupDate(date_create_from_format("Y-m-d", "2016-5-13"));
	$fu->SetFollowupReason("Updated reason");
	$fu->Save();
	$fu->Delete();
	$items = $fu->GetFollowupItemsForIncomeId(102);
	
	foreach ($items as $it)
	{
		echo "<br>Id:" . $it->GetId();
		echo "<br>IncomeId:" . $it->GetIncomeItemId();
		$da = $it->GetFollowupDate();
		echo "<br>Date:" . $da->format("d-m-Y");
		echo "<br>Reason:" . $it->GetFollowupReason();
		echo "<br>Action:" . $it->GetFollowupAction();
	}
	*/
	
	
	echo "*********************";
	echo "<br>Test IncomeItem";
	echo "<br>*********************";
	
	
	echo "<br>Test IncomeItem - Sets and Gets";
	echo "<br>*********************";
	$ii = new IncomeItem();
	$ii->SetId(1);
	$d = date_create_from_format("Y-m-d", "2016-07-25");
	$ii->SetIncomeDate($d);
	$ii->SetPaymentMethod(2);
	$ii->SetInvoiceNumber("I00394");
	$ii->SetFamily("Smith");
	$ii->SetName("Bob");
	$ii->SetDescription("This is the description.");
	$ii->SetCategory(3);
	$ii->SetAmount(25.02);
	$ii->SetNotes("Not to be refunded.");
	$ii->SetFollowupStatus(false);
	$ii->SetAdvisedBy("Bank statement.");
	
	/*
	echo "<br>*********************";
	echo "<br>Test IncomeItem - Delete income item";
	echo "<br>*********************";
	$ii = new IncomeItem();
	$ii->LoadIncomeItem(159);
	$ii->Delete();
	*/
	
	echo "<br>";
	echo "<br>*********************";
	echo "<br>Test IncomeItem - Get unreceipted income items";
	echo "<br>*********************";
	$ii = new IncomeItem();
	$items = $ii->GetUnreceiptedIncomeItems();
	
	foreach ($items as $i)
	{
		echo "<br>ID: " . $i->GetId();
		echo "<br>Date: " . $i->GetIncomeDate();
		echo "<br>PaymentMethod: " . getPaymentMethodDescription($i->GetPaymentMethod());
		echo "<br>InvoiceNumber: " . $i->GetInvoiceNumber();
		echo "<br>Family: " . $i->GetFamily();
		echo "<br>Name: " . $i->GetName();
		echo "<br>Description: " . $i->GetDescription();
		echo "<br>Category: " . getCategoryDescription($i->GetCategory());
		echo "<br>Amount: " . $i->GetAmount();
		echo "<br>Estimated: " . $i->GetAmountEstimated();
		echo "<br>Notes: " . $i->GetNotes();
		echo "<br>Followup Status: " . $i->GetFollowupStatus();
		echo "<br>Advised By: " . $i->GetAdvisedBy();
	}
	
	echo "<br>*********************";
	echo "<br>Test IncomeItem - Validation rules";
	echo "<br>*********************";
	
	$ii = new IncomeItem();
	$d = date_create_from_format("Y-m-d", "2016-7-25");
	$ii->SetIncomeDate($d);
	$ii->SetName("Bob McBobson");
	$ii->SetCategory(3);
	$ii->SetPaymentMethod(4);
	$ii->SetAmount(102.99);
	$ii->SetInvoiceNumber("T101");
	$ii->SetFamily("McBobson");
	$ii->SetDescription("Test Description");
	$ii->SetNotes("Test Notes");
	$ii->SetAdvisedBy("Test Advised");
	
	
	echo "<br>Payment Method: " . getPaymentMethodDescription($ii->GetPaymentMethod());
	echo "<br>Estimated: " . $ii->GetAmount();
	
	if ($ii->IsError())
	{
		$messages = $ii->GetErrorMessages();
		foreach ($messages as $m)
		{
			echo "<br>" . $m;
		}
	}
	
	$ii = new IncomeItem();
	$ii->LoadIncomeItem(160);
	$d = date_create_from_format("Y-m-d", "2016-7-27");
	$ii->SetIncomeDate($d);
	$ii->SetName("Bob McBobsonUpdate");
	$ii->SetCategory(2);
	$ii->SetPaymentMethod(5);
	$ii->SetAmount(102.98);
	$ii->SetInvoiceNumber("T101Update");
	$ii->SetFamily("McBobsonUpdate");
	$ii->SetDescription("Test Description UPDATE");
	$ii->SetNotes("Test Notes UPDATE");
	$ii->SetAdvisedBy("Test Advised Update");
	$ii->Save();
	
	if ($ii->IsError())
	{
		$messages = $ii->GetErrorMessages();
		foreach ($messages as $m)
		{
			echo "<br>" . $m;
		}
	}
	
	echo "*********************";
	echo "<br>Test followup items";
	echo "<br>*********************";
	
	$ii = new IncomeItem();
	$d = date_create_from_format("Y-m-d", "2016-7-27");
	$ii->SetIncomeDate($d);
	$ii->SetName("Bob McBobsonUpdate");
	$ii->SetCategory(2);
	$ii->SetPaymentMethod(5);
	$ii->SetAmount(102.98);
	$ii->SetInvoiceNumber("T101Update");
	$ii->SetFamily("McBobsonUpdate");
	$ii->SetDescription("Tseting followup items");
	$ii->SetNotes("Test Notes UPDATE");
	$ii->SetAdvisedBy("Test Advised Update");
	
	$fu = new FollowupItem();
	$fu->SetIncomeItemId();
	
	$ii->Save();
	
	/*
	echo "*********************";
	echo "<br>Training Fee Payments";
	echo "<br>*********************";
	
	
	$tfp = new TrainingFeePayment();
	$tfp->SetId(1);
	$tfp->SetMemberId(15);
	$tfp->SetIncomeId(25);
	$tfp->SetPeriodType("MONTHLY");
	$tfp->SetPeriodStart(date_create_from_format("Y-m-d", "2016-02-25"));
	$tfp->CalculatePeriodEndDate();
	
	echo "Dump - ";
	echo "Start: " . $tfp->GetPeriodStart()->format("d-m-Y");
	echo "End: " . $tfp->GetPeriodEnd()->format("d-m-Y");
	*/
	/*
	$date1 = date_create_from_format("Y-m-d", "2016-02-25");
	$date2 = clone $date1;
	
	echo "<br>DATE1: " . $date1->format("d-m-Y");
	//date_add($date2, date_interval_create_from_date_string("1 month"));
	$date2->add(date_interval_create_from_date_string("1 month"));
	
	echo "<br>Date1: " . $date1->format("d-m-Y");
	echo "<br>Date2: " . $date2->format("d-m-Y");
	*/
	/*
	var_dump($tfp);
	
	//$tfp->CalculatePeriodEndDate();
	
	var_dump($tfp);
	
	$tfp->SetNumCasualSessions(3);
	$tfp->SetNotes("Test notes");
	
	echo "Dump - ";
	echo "Start: " . $tfp->GetPeriodStart()->format("d-m-Y");
	echo "End: " . $tfp->GetPeriodEnd()->format("d-m-Y");
	*/
	/*
	$camp = new FundraisingCampaign();
	$camps = $camp->getCampaigns(null);
	
	foreach ($camps as $c)
	{
		echo "<br>ID: " . $c->getId();
		echo "<br>Name: " . $c->getName();
		echo "<br>";
	}
	
	
	$thisCampaign = new FundraisingCampaign();
	$myCampaign = $thisCampaign->loadFundraisingCampaign(25);
	
	echo "<br>25 - TRANSACTION LIST";
	$trans = $myCampaign->GetDetailedTransactions();
	
	foreach ($trans as $t)
	{
		echo "<br>Type: " . $t['type'];
		echo "<br>Date: " . $t['date']->format("d-M-Y");
		echo "<br>Name: " . $t['name'];
		echo "<br>Description: " . $t['description'];
		echo "<br>Amount: " . $t['amount'];
	}
	*/
	/*
	$thisCampaign = new FundraisingCampaign();
	$myCampaign = $thisCampaign->loadFundraisingCampaign(27);
	
	echo "<br>DELETING 27 - no  transactions should pass";
	echo "<br>" . $myCampaign->getId() . "</br>";
	echo "<br>" . $myCampaign->getName() . "</br>";
	echo "<br>" . $myCampaign->getDescription() . "</br>";
	$success = $myCampaign->delete();
	echo "Success? " . $success;
	*/
	/*
	$myCampaign ->addTransactionToCampaign(103, false);
	$myCampaign ->addTransactionToCampaign(106, false);
	$myCampaign ->addTransactionToCampaign(76, true);
	
	echo "<br>";
	$success = $myCampaign->delete();
	echo "Success? " . $success;
	
	*/
	
	/*
	echo "LOAD CAMPAIGNS";
	
	$thisCampaign = new FundraisingCampaign();
	//$theDate = date_create_from_format( "j-M-Y", "30-Jun-2016");
	$theDate = new DateTime();
	
	$campaigns = $thisCampaign->getCampaigns($theDate);
	foreach ($campaigns as $ca)
	{
		echo "<br>" . $ca->getName() . " - " . $ca->getId();
	}
	
	echo "<br>___";
	$campaigns = $thisCampaign->getCampaigns(null);
	
	foreach ($campaigns as $ca)
	{
		echo "<br>" . $ca->getName() . " - " . $ca->getId();
	}
	*/
	
/*	
	echo "<br>Get Total";
	
	$campaign = new FundraisingCampaign();
	$campaign->loadFundraisingCampaign(16);
	echo "<br> Amount Raised: " . $campaign->getTotalRaised();
	
	$ts = $campaign->getIncomeTransactions();
	foreach ($ts as $t)
	{
		echo "<br>Income TransId: " . $t;
	}
	
	$ts = $campaign->getExpenseTransactions();
	foreach ($ts as $t)
	{
		echo "<br>Expense TransId: " . $t;
	}
	
	if ($campaign->exceededTarget())
	{
		echo "<br>EXCEEDED";
	}
	*/
	
	/*
	echo "<br>ADD";
	
	$newCampaign = new FundraisingCampaign();
	$newCampaign->setName("Test");
	$newCampaign->setDescription("TestDescription");
	$newCampaign->setTarget(75);
	$newCampaign->setActive(date_create_from_format( "j-M-Y", "30-Jun-2016"));
	$newCampaign->save();
	
	echo "<br>DONE ADD";
	*/
	
	/*
	echo "<br>UPDATE";
	
	$updateCampaign = new FundraisingCampaign();
	$updateCampaign->loadFundraisingCampaign(14);
	$updateCampaign->setTarget(100);
	$updateCampaign->save();
	echo "<br>DONE";
	*/
	
	/*
	echo "DELETE";
	$deleteCampaign = new FundraisingCampaign();
	$deleteCampaign->loadFundraisingCampaign(14);
	
	echo "<br>Name: " . $deleteCampaign->getName();
	$deleteCampaign->delete();
	echo "DONE";
	*/
	
	/*
	echo "Adding Transactions";
	$transCampaign = new FundraisingCampaign();
	$transCampaign->loadFundraisingCampaign(18);
	$transCampaign->addTransactionToCampaign(103, false);
	$transCampaign->addTransactionToCampaign(106, false);
	$transCampaign->addTransactionToCampaign(76, true);
	
	echo "<br>DONE TRANSACTIONS";
	
	
	echo "<br>TRANS";
	$listCampaign = new FundraisingCampaign();
	$listCampaign->loadFundraisingCampaign(18);
	echo "LOADED";
	$ts = $listCampaign->getIncomeTransactions();
	foreach ($ts as $t)
	{
		echo "<br>TransId: " . $t;
	}
	
	echo "DONE";
	*/
	/*
	$thisCampaign->setName("");
	$thisCampaign->setDescription("tester description");
	$thisCampaign->setTarget(100);
	
	$activeDate = date_create_from_format( "j-M-Y", "15-Mar-2015");
	$thisCampaign->setActive($activeDate);
	$thisCampaign->setExpiry(null);
	
	if (!$thisCampaign->validate())
	{
		echo "ERROR";
		echo "<br>";
		
		$messages = $thisCampaign->getErrorMessages();
		foreach ( $messages as $m)
		{
			echo "<br>E: " . $m;
		}
	}
	else
	{
		echo "SUCCESS";
	}
	*/
?>