<?php
	include 'functions.php';
	require_once('config.php');
	session_start();

	// Connect to server and select database.
	($GLOBALS["___mysqli_ston"] = mysqli_connect(DB_HOST,  DB_USER,  DB_PASSWORD))or die("cannot connect, error: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . constant('DB_DATABASE')))or die("cannot select DB, error: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- changes width based on viewing device -->
		<title>Account Management</title>
		<link type="text/css" rel="stylesheet" href="style/base.css">
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Hammersmith+One" rel="stylesheet">
		
		<script>
		
		function onLoad() {
			document.getElementsByClassName("page-load")[0].classList.remove("page-load");
		}
				
		function $(id) {
			return document.getElementById(id);
		}
		
		function testAvatarValid(id) {
			if (testAvatarInputEmpty(id)) {
				return false; 
			}
			return testAvatarFormatValid(id);
		}
		
		function testAvatarFormatValid(id) {
			var fileName = $(id).value;			
			return fileName.substring(fileName.length-3).toLowerCase() == "png"
					|| fileName.substring(fileName.length-3).toLowerCase() == "jpg"
					|| fileName.substring(fileName.length-4).toLowerCase() == "jpeg"
					|| fileName.substring(fileName.length-3).toLowerCase() == "gif";
		}
		
		function testAvatarInputEmpty(id) {
			var fileName = $(id).value; 
			return fileName == "" || fileName == null || fileName.length < 4;
		}
		
		function validateAvatar(inputId, errorId) {
			if (!testAvatarValid(inputId)) {
				$(errorId).innerHTML = 
					testAvatarInputEmpty(inputId) ? 
						"" : "The file needs to be a png, jpg/jpeg, or gif image.";				
				return false; 
			}
			$(errorId).innerHTML = "";
			return true;
		}
		
		function validateAvatarSubmit(inputId, errorId) {
			if (!validateAvatar(inputId, errorId)) {
				event.preventDefault();
				$(errorId).innerHTML = 
					testAvatarInputEmpty(inputId) ? 
						"You need to select a file." 
							: "The file needs to be a png, jpg/jpeg, or gif image.";				
				return false; 
			}
			$(errorId).innerHTML = "";
			return true;
		}
		
		function testPasswordLength(id) {
			var password = $(id).value; 
			if (password == null) {
				return false; 
			}
			return password.length >= 8;
		}
		
		function testPasswordValidChar(id) {
			var password = $(id).value; 
			if (password == null) {
				return false; 
			}
			return !/[^a-zA-Z0-9\!\@\#\$\%\^\&\*]/.test(password);
		}
		
		function testCorrectPassword(id) {
			/* Placeholder for when account registration is actually possible. */
			return false;
		}
		
		function testEmailValid(id) {			
			var email = $(id).value; 
			if (email == null) {
				return false; 
			}
			return /^[\w!#$%&’*+\-/=?\^`{|}~]+(\.[\w!#$%&’*+\-/=?\^`{|}~]+)*@([a-zA-Z0-9-]+\.)+[a-zA-Z0-9-]{2,}$/.test(email);
		}
		
		function validateEmail(inputId, errorId) {
			if (!testEmailValid(inputId)) {
				$(errorId).innerHTML = "The email address is not valid.";				
				return false; 
			}
			$(errorId).innerHTML = "";			
			return true;
		}
		
		function validateEmailSubmit(inputId, passwordId, errorId) {
			if (!validateEmail(inputId, errorId)) {
				event.preventDefault();
				return false; 
			} 
			if (!testCorrectPassword(passwordId)) {
				event.preventDefault();
				$(errorId).innerHTML = "Incorrect Password";				
				return false; 
			}			
			$(errorId).innerHTML = "";
			return true;
		}
		
		function validatePasswordSubmit(inputId, inputId2, passwordId, errorId) {							
			if (!testPasswordValidChar(inputId)) {
				event.preventDefault();
				$(errorId).innerHTML = "The password can only contain letters, numbers, and the symbols !@#$%^&*";				
				return false; 
			}
			if (!testPasswordLength(inputId)) {
				event.preventDefault();
				$(errorId).innerHTML = "The password must have at least <br>8 characters.";				
				return false; 
			}
			if (!testPasswordMatch(inputId, inputId2)) {
				event.preventDefault();
				$(errorId).innerHTML = "Your new password does not match the other new password.";
				return false;
			}
			if (!testCorrectPassword(passwordId)) {
				event.preventDefault();
				$(errorId).innerHTML = "Incorrect Password <br><br> ";				
				return false; 
			}			
			$(errorId).innerHTML = "<br>";
			return true;
		}
		
		function validateDeleteSubmit(inputId, errorId) {
			if (!testCorrectPassword(inputId)) {
				event.preventDefault();
				$(errorId).innerHTML = "Incorrect Password";				
				return false; 
			}
			$(errorId).innerHTML = "";
			return true;
		}
		
		function measurePasswordStrength(id) {			
			var password = $(id).value;
			var mixedCase = password.toLowerCase() != password;
			var containsNumber = /\d/.test(password);
			var containsSymbol = /[\!\@\#\$\%\^\&\*]/.test(password);
			
			var strength = 1;			
			if (mixedCase) {
				strength++;
			}			
			if (containsNumber) {
				strength++;
			}			
			if (containsSymbol) {
				strength++;
			}			
			return strength;
		}
		
		function updatePasswordStrength(passwordId, barId, textId) {
			if (!testPasswordValidChar(passwordId)) {
				$(textId).innerHTML = "Only a-z, A-Z, 0-9, or !@#$%^&* allowed.";
				$(barId).value = 0;
				return;
			}
			if (!testPasswordLength(passwordId)) {
				$(textId).innerHTML = "Password must be at least 8 characters";
				$(barId).value = 0;
				return;
			}
			
			var strength = measurePasswordStrength(passwordId);
			$(barId).value = strength; 
			
			if (strength >= 4) {
				$(textId).innerHTML = "Password Strength: High";
			} else if (strength >= 2) {
				$(textId).innerHTML = "Password Strength: Medium";
			} else {
				$(textId).innerHTML = "Password Strength: Low";
			}
		}
		
		function testPasswordMatch(passwordId, passwordId2) {
			return $(passwordId).value == $(passwordId2).value || ($(passwordId).value == null && $(passwordId2).value == null);
		}
		
		function validatePasswordMatch(passwordId, passwordId2, passwordErrorId2) {
			if (!testPasswordMatch(passwordId, passwordId2)) {
				event.preventDefault();
				$(passwordErrorId2).innerHTML = "The passwords do not match.";
				return false; 
			}
			$(passwordErrorId2).innerHTML = "";
			return true; 
		}
		
		function testUsernameValid(id) {
			if (($(id).value == null) && ($(id).value.length >= 10)) {
				return false; 
			}			
			return $(id).value != "" && !/[^a-zA-Z0-9]/.test($(id).value);;			
		}
		
		function validateUsername(usernameId, errorId) {
			if (!testUsernameValid(usernameId)) {
				$(errorId).innerHTML = "Your username must only have numbers or letters and be 10 characters or less.";
				return false; 
			}
			$(errorId).innerHTML = "";
			return true; 
		}
		
		function validateRegistrationSubmit(emailId, emailErrorId, usernameId, usernameErrorId, passwordId, passwordErrorId, passwordId2, passwordErrorId2, avatarId, avatarErrorId) {
			var valid = true; 
			
			if(!validateEmail(emailId, emailErrorId)) {
				event.preventDefault();
				valid = false; 
			}
			
			if (!validateUsername(usernameId, usernameErrorId)) {
				event.preventDefault();
				valid = false; 
			}
			
			if (!testPasswordValidChar(passwordId) || !testPasswordLength(passwordId)) {
				event.preventDefault();
				$(passwordErrorId).innerHTML = !testPasswordValidChar(passwordId)
					? "Only a-z, A-Z, 0-9, or !@#$%^&* allowed."
						: "The password must have at least 8 characters.";								
				valid = false; 
			} else {
				$(passwordErrorId).innerHTML = "";
			}
						
			if (!validatePasswordMatch(passwordId, passwordId2, passwordErrorId2)) {
				event.preventDefault();
				valid = false; 
			}
			
			
			if(!testAvatarInputEmpty(avatarId) && !testAvatarValid(avatarId)) {
				event.preventDefault();
				$(avatarErrorId).innerHTML = 
					testAvatarInputEmpty(avatarId) ? 
						"" : "The file needs to be a png, jpg/jpeg, or gif image.";				
				valid = false; 
			}
			return valid;
		}
		
		</script>
	</head>
	<body class="page-load" onload="onLoad()">
		<div class="page"> <!-- Wrapper class for entire page. -->
			<div class="header"> <!-- Wrapper class for header that is full page width. -->
				<div class="header-centre"> <!-- Div centres contents. -->
					
					<div class="image-menu"> <!-- Div groups logo, name, and menu. -->
						<div class="sitelogo-cont"> <!-- Div contains logo. -->
							<a href="index.html"><img src="forumImages/logo2.png" alt="Site Logo" height="120" width="180" id="site-logo"></a> <!-- Hyperlinked logo -->
						</div>
						<div class="sitename"> <!-- Div contains name. -->
							<h1>Project Hamster</h1>
						</div>
						<div class="menu-links fade-link"> <!-- Div containing menu button. -->
							<ul>
								<li><a href="index.html" id="home-button">Home</a></li>
								<li><a href="about.html" id="about-button">About</a></li>
								<li><a href="forum.html" id="forum-button">Forum</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="body"> <!-- Unique contents of each page go in here. -->
				<div class="body-centre"> <!-- Majority of contents will go in here as it is centred -->
										
					<h2>Account Info</h2>					
					<!-- The user's account info. -->
					<div id="manage-account-info"> 						
						<!-- The user's avatar image. -->
						<div id="manage-avatar-div"> 
							<img src="forumImages/placeholder_avatar.jpg" alt="Username's Avatar">
						</div>
						
						<!-- The user's username and password. -->
						<p>Username: <br>
						username</p>		
						<p>Email: <br>
						user@email.com</p>						
					</div>
					
					<!-- Where the user changes the avatar image. -->
					<div id="manage-change-avatar">
						<h3>Change Avatar Image</h3> 
						<form method="post" action="http://webdevfoundations.net/scripts/formdemo.asp" onsubmit="return validateAvatarSubmit('manage_new_image', 'error_avatar');">		
							<label for="manage_new_image">New Image: </label><br>
							<input type="file" id="manage_new_image" name="newAvatarImage" onchange="return validateAvatar('manage_new_image', 'error_avatar');">
							<br>
							<input type="submit" value="Submit">		
						</form>
						<div id="error_avatar">								
							<br>
						</div>
					</div>
					
					<!-- Where the user can make changes to their account. -->
					<div id="manage-second-row"> 
						
						<!-- Where the user changes the email. -->	
						<div id="manage-change-email">
							<h3>Change Email</h3> 	
							<form method="post" action="http://webdevfoundations.net/scripts/formdemo.asp" onsubmit="return validateEmailSubmit('manage_new_email', 'manage_password_for_email', 'error_email');">
								<label for="manage_new_email">New Email: </label><br>
								<input type="text" id="manage_new_email" name="newEmail" value="" onchange="return validateEmail('manage_new_email', 'error_email');">
								<br>
								
								<label for="manage_password_for_email">Current Password: </label><br>
								<input type="password" id="manage_password_for_email" name="passwordEmailChange" value=""><br>
								
								<input type="submit" value="Submit">
							</form>
							<div id="error_email">
								<br>
							</div>
						</div>
						
						<!-- Where the user changes the password. -->	
						<div id="manage-change-password">
							<h3>Change Password</h3> 	
							<form method="post" action="http://webdevfoundations.net/scripts/formdemo.asp" onsubmit="return validatePasswordSubmit('manage_new_password', 'manage_new_password2', 'manage_password_for_password', 'error_password');">
								<label for="manage_new_password">New Password: </label><br>
								<input type="password" id="manage_new_password" name="newPassword" value="" onkeyup="updatePasswordStrength('manage_new_password', 'password_strength_bar', 'password_strength_text')"><br>
								
								<div id="password_strength">
									<h5 id="password_strength_text">Password Strength: </h5>
									<progress id="password_strength_bar" value ="0" max="4"></progress>
								</div>
								
								<label for="manage_new_password2">Retype New Password: </label><br>
								<input type="password" id="manage_new_password2" name="newPassword2" value="">
								<br>
								
								<label for="manage_password_for_password">Current Password: </label><br>
								<input type="password" id="manage_password_for_password" name="passwordPasswordChange" value=""><br>
								
								<input type="submit" value="Submit">
							</form>
							<div id="error_password">
								<br><br>
							</div>
						</div>
						
						<!-- Where the user deletes the account. -->
						<div id="manage-delete-account">
							<h3>Delete Account</h3> 
							<form method="post" action="http://webdevfoundations.net/scripts/formdemo.asp" onsubmit="return validateDeleteSubmit('manage_deletion_password', 'error_deletion');">
								<label for="manage_deletion_password">Current Password: </label><br>
								<input type="password" id="manage_deletion_password" name="passwordAccountDeletion" value=""><br>
								<input type="submit" value="Warning! This cannot be undone!">		
							</form>							
							<div id="error_deletion">
								<br>
							</div>
						</div>
						
					</div>
					
				</div>
			</div>
			<div class="footer"> <!-- Full width container for the footer -->
				<div class="footer-centre"> <!-- Centered footer for other footer divs -->
					<a href="index.html" id="home-small">Home</a>
					<a href="about.html" id="about-small">About</a>
					<a href="termOfService.html" id="tos-small">Terms of Service</a>
					<a href="privacyPolicy.html" id="privacy-small">Privacy Policy</a>
					<a href="forum.html" id="forum-small">Forum</a>
					<a href="account.html" id="account-manage-small">Account Management</a>
				</div>
			</div>
		</div>
	</body>
</html>

<script> 
	<?php 
	if (!isLoggedIn()) {
		echo "document.getElementsByClassName('body-centre')[0].innerHTML = '<center>Please log in before managing your account.</centre>'";
	}
	?>	
</script>
