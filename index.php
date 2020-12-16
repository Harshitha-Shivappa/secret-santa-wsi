<?php
include 'config.php';
// Initialize the session
/* session_start();
  include 'config.php';
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: secret.php");
    exit;
} */

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else if(!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
        $email_err = "Please enter correct format.";
    }
    else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT Email, Password FROM users WHERE Email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $email, $cpassword);
                    if(mysqli_stmt_fetch($stmt)){
                        /* session_destroy(); */
                        if($password == $cpassword) {
                      session_start();
                      $_SESSION["email"] = $email;
                      
                        /* if(password_verify($password, $password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["email"] = $email;  */                           
                            
                            // Redirect user to welcome page
                            header("location: secret.php");
                        }
                        else {
                            $password_err = "You have entered wrong password";
                        }
                    }
                } else{
                    // Display an error message if email doesn't exist
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/icon.png" type="image/x-icon">
    <title>Login</title>
  <!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css"> -->
    <style type="text/css">
        .wrapper{ 
        width: 500px; 
        padding: 1px; 
        text-align: center;
        margin-left: 500px;
        margin-top: 40px;
        background-color: red;
        border-radius: 10px;
    }
    body {
        background-image: url('images/bg.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        font-family: "Times New Roman", Times, serif;
        font-size: 20px;
    }
    h2, p {
        color: white;
        padding-bottom:10px;
        font-weight: bolder;
    }
    .text {
        color: black;
        margin-right:35px;
        width: 300px;
        float: right;
        height: 25px;
    }
    label {
        color: white;
        margin-left: 5%;
        float: left;
    }
    .btn {
        background-color: #1aff1a;
        font-family: "Times New Roman", Times, serif;
        font-size: 20px;
        font-weight: bolder;
        margin: 20px 50px;
    }
    .help-block {
        color: black;
        margin-left: 60px;
        font-weight: bolder;
    }
    </style>
</head>
<body oncontextmenu="return false">
    <div class="wrapper">
        <h2>Login</h2>
        <p>Hi Santa!! Please fill in your crendentials.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" class="text" name="email" class="form-control" value="<?php echo $email; ?>"> <br>
              <br>  <span class="help-block"><?php echo $email_err; ?></span>
            </div> <br>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label> 
                <input type="password" class="text" name="password" class="form-control"><br>
               <br> <span class="help-block"><?php echo $password_err; ?></span>
            </div> <br>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>    
</body>
</html>