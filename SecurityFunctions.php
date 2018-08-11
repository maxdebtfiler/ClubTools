<?php
	function checkSecurity($page)
	{
		echo("B4 ");
		$security = $_SESSION['security'];
		echo("B5 ");
		$sql = "SELECT token_expiry, role FROM users WHERE id = '" . $security["user"] . "' AND token = '" . $security["token"] . "'";
		echo("B6 ");
		echo sql;
		$result = mysql_query($sql);
				
		if ( mysql_num_rows($result) == 0 )
		{
			$error = array( "page" => $page, "message" => "There is a problem with your session details. Please login again." );
			$_SESSION['errors'] = $error;
			header( 'Location: http://www.artistansolutions.com/ClubTools/Login.php' );
		}
		else
		{
			$row = mysql_fetch_array($result);
			$expiry = strtotime($row['token_expiry']);
			if ( time() >= $expiry )
			{
				$error = array( "page" => $page, "message" => "Your session has expired. Please login again." );
				$_SESSION['errors'] = $error;
				header( 'Location: http://www.artistansolutions.com/swimming/Login.php' );
			}
			else
			{
				$today = date("Y-m-d H:i:s");
				$updatedExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes', strtotime($today)));
				$sql = "UPDATE users SET token_expiry = '" . $updatedExpiry . "' WHERE id = '" . $security["user"] . "'";
				
				$security["role"] = $row['role'];
				$_SESSION['security'] = $security;
				
				mysql_query($sql);
			}
		}
	}
	
	function cancelSession()
	{
		$security = $_SESSION['security'];
		$sql = "UPDATE users SET token = null WHERE id = '" . $security["user"] . "'";
		mysql_query($sql);
		
		$error = array( "page" => "MainMenu.php", "message" => "You do not have administrator access so cannot make maintenance changes." );
		$_SESSION['errors'] = $error;
		header( 'Location: http://www.artistansolutions.com/ClubTools/Login.php' );
	}
	
	function logout()
	{
		$security = $_SESSION['security'];
		$sql = "UPDATE users SET token = null WHERE id = '" . $security["user"] . "'";
		mysql_query($sql);
		
		unset($_SESSION['errors']);
		unset($_SESSION['security']);
		header( 'Location: http://www.artistansolutions.com/ClubTools/Login.php' );
	}
?>