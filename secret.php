<?php
    include 'config.php';
    session_start();
    $query = "SELECT City FROM users_data";
    $result = mysqli_query($link, $query);
    
    $city = $address = "";
    $city_err = $address_err = $email_err = "";
    $status = "Email not sent";
    $email = $_SESSION["email"];
    if($email == "") {
        header("location: index.php");
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){ 
        $city = $_POST["city"];
         $esql = "SELECT Email FROM santa WHERE Email = ?";
        //Checking for the existing email
        if($estmt = mysqli_prepare($link, $esql)) {
            
            mysqli_stmt_bind_param($estmt, "s", $param_semail);

            $param_semail = $email;

            if(mysqli_stmt_execute($estmt)) {
                
                mysqli_stmt_store_result($estmt);
                if(mysqli_stmt_num_rows($estmt)==1){
                    $email_err = "You have already selected your child.";
                }
            }
        }
        //working
        /* $que = "SELECT * FROM users_data";
        $res = mysqli_query($link,$que);
        while($rows=mysqli_fetch_assoc($res)) {
            echo $rows['Emp_ID'];
            echo $rows['Employee_Name'];
            echo $rows['City'];
            echo $rows['Email_ID'];
        } */

        //Fetch mail based on city selected
        $child_email="";
        $queries = "SELECT * FROM `users_data` WHERE City='$city'";
        $run = mysqli_query($link,$queries);
        while($row = mysqli_fetch_array($run)) {
          //  echo $row['Email_ID'];
            $child_email = $row['Email_ID'];
        }
       

        //Address validation
        if(empty(trim($_POST["address"]))) {
            $address_err = "Please enter deliverable address.";
        } else if(strlen($_POST["address"]) <=3 || strlen($_POST["address"]) >= 150) {
            $address_err = "Address length should be greater than 3";
        }
        else {
            $address = trim($_POST["address"]);
        }
        // Login and selected mail should not be same 
        if($email == $child_email){
            $city_err = "Please select some other city";
        }
        if(empty($email_err) && empty($address_err) && empty($city_err)) {
            $sql = "INSERT INTO santa (Email, City, C_email, Address, Email_Status) VALUES (?, ?, ?, ?, ?)";
            function msg($mess){
                echo '<script type=\'text/javascript\'>'; 
                echo 'alert("'.$mess.'");'; 
                echo 'document.location.href = "index.php";';
                echo '</script>';
                session_destroy();
            }

            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssss", $param_email, $param_city, $param_cemail, $param_address, $param_status);
                
                $param_email = $email;
                $param_city = $city;
                $param_cemail = $child_email;
                $param_address = $address;
                $param_status = $status;
               
                if(mysqli_stmt_execute($stmt)){
                    //Delete the particular record based on city selected
                    $dquery = "DELETE FROM users_data WHERE City = '$city'";
                    
                    $dquery_run = mysqli_query($link, $dquery);
                    if($dquery_run) {
                        //Sending mail
                        $body = "You have selected your child and that is $child_email. Gift them to make them feel special";
                        $subject = "Secret Santa";
                        $headers = "From: harshitha.shivappa1195@gmail.com";
                        if(mail ($email, $subject, $body, $headers)) { 
                            
                            $status = "Email sent";
                            $msql = "UPDATE santa SET Email_status='$status' WHERE Email='$email'";
                           
                                if(mysqli_query($link, $msql)) {
                                    msg("😎Well Done!!! Your child is waiting for you 🤗 Your child email is ".$child_email. " You will get a mail regarding the child.");
                                    /* echo '<script type="text/javascript">'; 
                                    echo 'alert("😎Well Done!!! Your child is waiting for you 🤗 You will get to know about your child via email.");'; 
                                    echo 'document.location.href = "index.php";';
                                    echo '</script>';
                                    session_destroy(); */
                                }
                        } else {
                            msg("😎Well Done!!! Your child is waiting for you 🤗 Your child email is ".$child_email. " There is some problem in sending mail. Sorry!!! We will let you know your child name shortly.");
                           /*  echo '<script type="text/javascript">'; 
                            echo 'alert("😎Well Done!!! Your child is waiting for you 🤗 There is some problem in sending mail. Sorry!!! We will let you know your child name shortly.");'; 
                            echo 'document.location.href = "index.php";';
                            echo '</script>';   
                            session_destroy(); */
                        }
                    }
                   
                    
                   /*  function msg($mess){
                        echo '<script type=\'text/javascript\'>'; 
                        echo 'alert("'.$mess.'");'; 
                        echo '</script>';
                    } */


                    
                } 
                else {
                    echo "Something went wrong";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Child</title>
    <link rel="icon" href="images/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
    body {
        background-image: url('images/bg.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        font-family: "Times New Roman", Times, serif;
        font-size: 20px;
    }
    h2 {
        color: white;
        padding-bottom:10px;
        font-weight: bolder;
    }
    .wrapper{ 
        width: 500px; 
        padding: 5px; 
        text-align: center;
        margin-left: 500px;
        margin-top: 40px;
        background-color: red;
        border-radius: 10px;
    }
    .text {
        color: black;
        margin-left:18px;
        width: 300px;
    }
    textarea {
        margin-left: 10px;
        margin-right:30px;
        width: 250px;
    }
    select {
        margin-right: 90px;
        height: 30px;
        width: 150px;
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
        margin-bottom: 10px;
    }
    .help-block {
        color: black;
    }  
    </style>
</head>
<body>
<div class="wrapper">
<h2> Secret Santa </h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<div class="form-group">
<label>Email ID</label>
<input type="text" class="text" value="<?php echo $email ?>" disabled> </div>
<div class="form-group" <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
    <label>City</label>
    <select name="city" id="sel">
        <?php while($row1 = mysqli_fetch_assoc($result)):;?>
        <option name="cty" value=<?php echo $row1['City']?>><?php echo $row1['City']?> </option>
        <?php endwhile; ?> 
    </select>
    <span class="help-block"><?php echo $city_err; ?></span>
</div>

<div class="form-group" <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
    <label>Address</label>
    <textarea name="address"> </textarea> <br> 
    <span class="help-block"><?php echo $address_err; ?></span>
</div>
<span class = "help-block"> <?php echo $email_err; ?> </span>
<input type="submit" class="btn" value="Submit">
</form>
</div>

</body>
</html>