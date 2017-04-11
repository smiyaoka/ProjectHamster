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
		<title>Account Registration</title>
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
			return ((password.length >= 8) && (password.length <= 20));
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
		
		function validatePasswordSubmit(inputId, passwordId, errorId) {							
			if (!testPasswordValidChar(inputId)) {
				event.preventDefault();
				$(errorId).innerHTML = "The password can only contain letters, numbers, and the symbols !@#$%^&*";				
				return false; 
			}
			if (!testPasswordLength(inputId)) {
				event.preventDefault();
				$(errorId).innerHTML = "The password must be between 8-20 characters.";				
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
				$(textId).innerHTML = "The password must be between 8-20 characters.";
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
			if (($(id).value == null) || ($(id).value.length >= 10)) {
				return false; 
			}			
			return $(id).value != "" && !/[^a-zA-Z0-9]/.test($(id).value);;			
		}
		
		function validateUsername(usernameId, errorId) {
			if (!testUsernameValid(usernameId)) {
				$(errorId).innerHTML = "Your username must only have numbers or letters and 10 characters or less.";
				return false; 
			}
			$(errorId).innerHTML = "";
			return true; 
		}
		
		function validateRegistrationSubmit(usernameId, usernameErrorId, passwordId, passwordErrorId, passwordId2, passwordErrorId2) {
			var valid = true; 
			
			if (!validateUsername(usernameId, usernameErrorId)) {
				event.preventDefault();
				valid = false; 
			}
			
			if (!testPasswordValidChar(passwordId) || !testPasswordLength(passwordId)) {
				event.preventDefault();
				$(passwordErrorId).innerHTML = !testPasswordValidChar(passwordId)
					? "Only a-z, A-Z, 0-9, or !@#$%^&* allowed."
						: "The password must be between 8-20 characters.";								
				valid = false; 
			} else {
				$(passwordErrorId).innerHTML = "";
			}
						
			if (!validatePasswordMatch(passwordId, passwordId2, passwordErrorId2)) {
				event.preventDefault();
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
				<div id="registration-div">						
					<h2>Make an Account</h2>											
						<div>
							<?php
								if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
									echo '<ul class="err">';
									foreach($_SESSION['ERRMSG_ARR'] as $msg) {
										echo '<li>',$msg,'</li>'; 
									}
									echo '</ul>';
									unset($_SESSION['ERRMSG_ARR']);
								}
							?>						
						</div>						
						<form method="post" action="register.php" onsubmit="return validateRegistrationSubmit('register_username', 'registration-error-username', 'register_password', 'registration-error-password', 'register_password2', 'registration-error-password2');">		
							
							<label for="register_username">Username: </label><br>
								<input type="text" id="register_username" name="registerUsername" value="" onchange="validateUsername('register_username', 'registration-error-username')">
								<span id="registration-error-username" class="registration-error"></span>
								<br>
							<label for="register_password">Password: </label><br>
								<input type="password" id="register_password" name="registerPassword" value="" onkeyup="updatePasswordStrength('register_password', 'password_strength_bar', 'password-strength-text')">
								<span id="registration-error-password" class="registration-error"></span>
								
								<div id="password_strength">
									<h5 id="password-strength-text">Password Strength: </h5>
									<progress id="password_strength_bar" value ="0" max="4"></progress>
								</div>
								
								<label for="register_password2">Retype Password: </label><br>
								<input type="password" id="register_password2" name="registerPassword2" value="" onchange="validatePasswordMatch('register_password', 'register_password2', 'registration-error-password2')">
								<span id="registration-error-password2" class="registration-error"></span>
							
							<br>
							<br>
							<input type="submit" value="Submit">		
						</form>
						<div id="error_avatar">								
							<br>
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
				</div>
			</div>
		</div>
	</body>
</html>

<script> 
	<?php 
	if (isLoggedIn()) {
		echo "document.getElementsByClassName('body-centre')[0].innerHTML = '<center>Please log out before registering a new account.</centre>'";
	}
	?>	
</script>
