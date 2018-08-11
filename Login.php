	<?php
		session_start();
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
		<TITLE>Login to Swimming Time Records</TITLE>
		
		<script>
		function setFocus()
		{
			document.getElementById("username").focus();
		}
		</script>
	</head>
	
	<body onload="setFocus()">
	<div id="header">
		<h1>Club Tools</h1>
		<h2>Reducing the pain of club administration</h2>
	</div>
	
	<div style="clear: both;">&nbsp;</div>
	
	<div id="content">
		<div><img src="Images/Swimmer.jpg" alt="" /></div>
		
		<div id="colOne">
			<div id="menu1">
				<ul>
					<li><a href="http://www.artistansolutions.com/ClubTools/MainMenu.php">Home</a></li>
				</ul>
			</div>
			<div class="margin-news">
				<h2>News</h2>
				<p>
					<div id="NewsItem">
						<div id="NewsTitle"><a href="index.php?news&nid=1"><h1>Website created!...</h1></a></div>
						<div id="NewsDate"><a href="index.php?news&nid=1">04-12-2015</a></div>
						<div id="NewsOverview"><a href="index.php?news&nid=1">This website is still under construction, please visit us later!</a></div>
					</div>
					<div style="clear:both;"></div>
				</p>
			</div>
		</div>
	
		<div id="colTwo">
			<h2>Login</h2>
			<form action="Login.php" method="post">
			
			<?php
				$con = mysql_connect("localhost", "artista9_swim", "Mclarenf1") or die(mysql_error());
				mysql_select_db("artista9_clubmgmt") or die(mysql_error());
				
				$directPage = "MainMenu.php";
									
				if ( $_POST['login'] )
				{
					$row = null;
					$sql = "SELECT password, role, failed_attempts FROM users WHERE id = '" . $_POST['username'] . "'";
					$userResult = mysql_query($sql);
					
					if ( mysql_num_rows($userResult) == 0 )
					{
						echo ("Does not exist");
						displayLoginForm("User does not exist");
					}
					else
					{
						echo ("P Check");
						$row = mysql_fetch_array($userResult);
						if (sha1($_POST['password']) == $row['password'] )
						{
							echo ("P Checked - pass");
							updateFailedAttempts( $_POST['username'], 0 );
							setToken( $_POST['username'], $row['role'] );
							header( "Location: " . $directPage ) ;
						}
						else
						{
						echo ("P Checked - fail");
							//$failed = $row['failed_attempts'] + 1;
							//updateFailedAttempts( $_POST['username'], $failed );
							displayLoginForm("Invalid password");
						}
					}	
				}
				else 
				{
					displayLoginForm($errorMsg);
				}
				
				function displayLoginForm( $errorMessage )
				{
				    echo "<table border=1>
							<tr>
								<td>Username</td>
								<td><input type=\"text\" id=\"username\" name=\"username\" value=\"\"/>
							</tr>
							<tr>
								<td>Password</td>
								<td><input type=\"password\" id=\"password\" name=\"password\" value=\"\"/>
							</tr>
							</table>
							<input type=\"submit\" name=\"login\" value=\"Login\"/>";
				}
				
				function updateFailedAttempts( $userId, $failedAttempts )
				{
					$sql = "UPDATE users SET failed_attempts = " . $failedAttempts . " WHERE id = '" . $userId . "'";
					mysql_query($sql);
				}
				
				function setToken( $userId, $role )
				{
					$token = uniqid();
					$today = date("Y-m-d H:i:s");
					$expiry = date('Y-m-d H:i:s', strtotime('+60 minutes', strtotime($today)));
					
					$security = array( "user" => $userId, "token" => $token, "role" => $role );
					$_SESSION['ct_security'] = $security;
					
					$sql = "UPDATE users SET token = '" . $token . "', token_expiry = '" . $expiry . "', last_login = '" . $today . "' WHERE id = '" . $userId . "'";
					mysql_query($sql);
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