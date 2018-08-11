<?php
	include 'CommonFunctions.php';
	include 'FundraisingCampaign.php';
	include 'TrainingFeePayment.php';
	include 'Expense.php';
	session_start();
	date_default_timezone_set('Australia/Adelaide');
	
	init();
	$_SESSION['databaseConnection'] = mysqli_connect("localhost", "onkaswim_toolusr", "Mclarenf1", "onkaswim_clubtoolstest") or die(mysql_error());
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	echo "<br>+USING THE onkaswim_clubtoolstest database+";
	echo "<br>+++++++++++++++++++++++++++++++++++++++++++";
	
	
	echo "<br>*********************";
	echo "<br>Test Expense";
	echo "<br>*********************";
	echo "<br>Test Expense - Sets and Gets";
	echo "<br>*********************";
	
	$exp = new Expense();
	/*$exp->SetId(15);
	$exp->SetCreated(date_create_from_format("Y-m-d", "2016-7-14"));
	$exp->SetPayee("Max");
	$exp->SetFamilyCode("KEMP");
	$exp->SetInvoiceNumber("INV0020");
	$exp->SetDescription("expense description");
	$exp->SetDueDate(date_create_from_format("Y-m-d", "2016-7-15"));
	$exp->SetCategory(11);
	$exp->SetAmount(500.05);
	$exp->SetImage("someblobbystuff");
	$exp->SetImageType("/pdf");
	$exp->SetImageLength(1000);
	$exp->SetImageName("image name");
	$exp->SetFileName("Invoices/filename.fmn");
	$exp->SetNotes("Some notes");
	
	echo "<br>ID (should be 15): " . $exp->GetId();
	echo "<br>Created (should be 14-7-2016): " . $exp->GetCreated()->format("d-m-Y");
	echo "<br>Payee (should be Max): " . $exp->GetPayee();
	echo "<br>FamilyCode (should be KEMP): " . $exp->GetFamilyCode();
	echo "<br>InvoiceNumber (should be INV0020): " . $exp->GetInvoiceNumber();
	echo "<br>Description (should be expense description): " . $exp->GetDescription();
	echo "<br>DueDate (should be 15-7-2016): " . $exp->GetDueDate()->format("d-m-Y");
	echo "<br>Category (should be 11): " . $exp->GetCategory();
	echo "<br>Amount (should be 500.05):" . $exp->GetAmount();
	echo "<br>Image (should be someblobbystuff): " . $exp->GetImage();
	echo "<br>ImageType (should be /pdf): " . $exp->GetImageType();
	echo "<br>ImageLength (should be 1000): " . $exp->GetImageLength();
	echo "<br>ImageName (should be image name): " . $exp->GetImageName();
	echo "<br>FileName (should be 'Invoices/filename.fmn'): " . $exp->GetFileName();
	echo "<br>Notes (should be Some notes): " . $exp->GetNotes();
	*/
	echo "<br>*********************";
	echo "<br>Test Expense - Load an Expense";
	echo "<br>*********************";
	
	$exq = $exp->LoadExpense(85);
	echo "<br>ID: " . $exq->GetId();
	echo "<br>Created: " . $exq->GetCreated();
	echo "<br>Payee: " . $exq->GetPayee();
	echo "<br>FamilyCode: " . $exq->GetFamilyCode();
	echo "<br>InvoiceNumber: " . $exq->GetInvoiceNumber();
	echo "<br>Description: " . $exq->GetDescription();
	echo "<br>DueDate: " . $exq->GetDueDate();
	echo "<br>Category: " . $exq->GetCategory();
	echo "<br>Amount:" . $exq->GetAmount();
	echo "<br>Image: " . $exq->GetImage();
	echo "<br>ImageType: " . $exq->GetImageType();
	echo "<br>ImageLength: " . $exq->GetImageLength();
	echo "<br>ImageName: " . $exq->GetImageName();
	echo "<br>FileName: " . $exq->GetFileName();
	echo "<br>Notes: " . $exq->GetNotes();
	echo "<br>Approvals: " . count($exq->GetApprovals());
	echo "<br><br>";
	var_dump($exq);
		
	/*
	echo "<br>";
	echo "<br>*********************";
	echo "<br>Test Expense - Get Unpaid Expenses";
	echo "<br>*********************";
	$expenses = $exp->GetUnpaidExpenses();
	foreach ($expenses as $exs)
	{
		echo "<br>ID: " . $exs->GetId();
		echo "<br>Created: " . $exs->GetCreated();
		echo "<br>Payee: " . $exs->GetPayee();
		echo "<br>FamilyCode: " . $exs->GetFamilyCode();
		echo "<br>InvoiceNumber: " . $exs->GetInvoiceNumber();
		echo "<br>Description: " . $exs->GetDescription();
		echo "<br>DueDate: " . $exs->GetDueDate();
		echo "<br>Category: " . $exs->GetCategory();
		echo "<br>Amount:" . $exs->GetAmount();
		if ( $exs->GetImageLength() > 0 )
		{
			echo "<br>Image: exists";
		}
		else
		{ 
			echo "<br>Image: does not exist";
		}
		echo "<br>ImageType: " . $exs->GetImageType();
		echo "<br>ImageLength: " . $exs->GetImageLength();
		echo "<br>ImageName: " . $exs->GetImageName();
		echo "<br>FileName: " . $exs->GetFileName();
		echo "<br>Notes: " . $exs->GetNotes();
		
		echo "<br>";
	}
	*/
	/*
	echo "<br>*********************";
	echo "<br>Test Expense - Save New Expense";
	echo "<br>*********************";
	$ext = new Expense();
	$ext->SetCreated(date_create_from_format("Y-m-d", "2016-7-14"));
	$ext->SetPayee("Max");
	$ext->SetFamilyCode("KEMP");
	$ext->SetInvoiceNumber("INV0020");
	$ext->SetDescription("expense description");
	$ext->SetDueDate(date_create_from_format("Y-m-d", "2016-7-15"));
	$ext->SetCategory(11);
	$ext->SetAmount(500.05);
	$ext->SetImage("someblobbystuff");
	$ext->SetImageType("/pdf");
	$ext->SetImageLength(1000);
	$ext->SetImageName("image name");
	$ext->SetFileName("Invoices/filename.fmn");
	$ext->SetNotes("Some notes");
	$ext->Save();
	echo "<br>Success if it has an ID: " . $ext->GetId();
	
	echo "<br>*********************";
	echo "<br>Test Expense - Update Expense";
	echo "<br>*********************";
	$exu = new Expense();
	$exv = $exu->LoadExpense(129);
	//$ext->SetCreated(date_create_from_format("Y-m-d", "2016-7-14"));
	$exv->SetPayee("Maxypoo");
	
	//$ext->SetFamilyCode("KEMP");
	//$ext->SetInvoiceNumber("INV0020");
	//$ext->SetDescription("expense description");
	//$ext->SetDueDate(date_create_from_format("Y-m-d", "2016-7-15"));
	//$ext->SetCategory(11);
	//$ext->SetAmount(500.05);
	//$ext->SetImage("someblobbystuff");
	//$ext->SetImageType("/pdf");
	//$ext->SetImageLength(1000);
	//$ext->SetImageName("image name");
	//$ext->SetFileName("Invoices/filename.fmn");
	//$ext->SetNotes("Some notes");
	
	$exv->Save();
	echo "<br>Done - check database for change";
	
	echo "<br>*********************";
	echo "<br>Test Expense - Delete Expense";
	echo "<br>*********************";
	$exv->Delete();
	echo "<br>Done - check database for change";
	*/
?>