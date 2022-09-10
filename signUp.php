<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM test WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO test (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<html>
    <head>
        <title>
            Signup
</title>
</head>
<meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" 
href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="Styles.css">
 <script 
src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
 <script 
src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></scrip
t>
 <script 
src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<body style="background-color:#303030;color:white;">
<div style="background-color:#212121">
<button type="button" class = "TopButton" onclick="location.href='index.php'">Homepage</button>
<button type="button" class = "TopButton" onclick="location.href='login.php'" style="float:right">Login</button>
<button type="button" class = "TopButton" onclick="location.href='signUp.php'" style="float:right">Signup</button>
<br>
</div>
<br><br><br>
<div class="SignUp">
<br>
<p style = "text-align:center;font-size:24">Sign Up</p><br>
<?php 
    if(!empty($login_err)){
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }        
?>
<form name="Signup" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<label style = "margin:10px;" for="exampleFormControlInput1">Entre a User ID:</label><br>
<input type="text" name="username" style = "margin:10px;width:470px;height:35px;background-color: #303030;border-radius: 5px;border-style:solid;border-color:#424242;color:white;" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
<span class="invalid-feedback"><?php echo $username_err; ?></span><br>
<label style = "margin:10px;" for="exampleFormControlInput1">Entre a Password:</label><br>
<input type="password" name="password" style = "margin:10px;width:470px;height:35px;background-color: #303030;border-radius: 5px;border-style:solid;border-color:#424242;color:white;" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
<span class="invalid-feedback"><?php echo $password_err; ?></span>
<label style = "margin:10px;" for="exampleFormControlInput1">Re-enter Password:</label><br>
<input type="password" name="confirm_password" style = "margin:10px;width:470px;height:35px;background-color: #303030;border-radius: 5px;border-style:solid;border-color:#424242;color:white;" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
<span class="invalid-feedback"><?php echo $confirm_password_err; ?></span><br><br>
<center>
<button type="submit" class = "WebButton">Submit</button>
</center>
</form>
</div>
</body>
</html>