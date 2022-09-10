<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
    header("location: dashboard.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM test WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: dashboard.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
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
            Login
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
<div class="Login">
<br>
<p style = "text-align:center;font-size:24">Login</p><br>
<?php 
    if(!empty($login_err)){
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }        
?>
<form name="Login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<label style = "margin:10px;" for="exampleFormControlInput1">Enter Your Email:</label><br>
<input type="text" name="username" style = "margin:10px;width:470px;height:35px;background-color: #303030;border-radius: 5px;border-style:solid;border-color:#424242;color:white;" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
<span class="invalid-feedback"><?php echo $username_err; ?></span><br>
<label style = "margin:10px;" for="exampleFormControlInput1">Enter Your Password:</label><br>
<input type="password" name="password" style = "margin:10px;width:470px;height:35px;background-color: #303030;border-radius: 5px;border-style:solid;border-color:#424242;color:white;" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
<span class="invalid-feedback"><?php echo $password_err; ?></span><br>
<center>
<button type="submit" class = "WebButton">Submit</button><br><br>
Don't have an account? <a href="signUp.php">Sign up now</a>.
</center>
</form>
</div>
</body>
</html>