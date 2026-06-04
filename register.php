<?php
session_start();

$error = '';

if (isset($_POST["register"])) {

    require_once "conn.php";

    $username = mysqli_real_escape_string($connection, trim($_POST['username']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        $check = "SELECT * FROM $tablelogin WHERE username='$username'";
        $check_result = mysqli_query($connection, $check);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists.";
        } else {

            $hashed = hash('sha256', $password);

            $sql = "INSERT INTO $tablelogin (username, password)
                    VALUES ('$username', '$hashed')";

            if (mysqli_query($connection, $sql)) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed.";
            }
        }
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="img/tabicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Yuji+Boku&display=swap" rel="stylesheet">

<title>Register</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Yuji Boku', serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:url('loginphoto.jpg') center center/cover no-repeat fixed;
}

/* SAME STYLE AS LOGIN CARD */
.form-box{
    width:380px;
    text-align:center;
    color:white;
}

/* Title */
.form-box h1{
    font-size:42px;
    letter-spacing:6px;
    margin-bottom:30px;
    color:#fff;
}

/* Inputs */
.input-box{
    position:relative;
    margin:25px 0;
}

.input-box input{
    width:100%;
    padding:12px 5px;
    background:transparent;
    border:none;
    border-bottom:1px solid rgba(255,255,255,0.6);
    outline:none;
    color:white;
    font-size:14px;
}

.input-box label{
    position:absolute;
    left:5px;
    top:10px;
    color:rgba(255,255,255,0.7);
    font-size:14px;
    pointer-events:none;
    transition:0.3s;
}

.input-box input:focus ~ label,
.input-box input:valid ~ label{
    top:-12px;
    font-size:11px;
    color:white;
}


button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid rgba(255,255,255,0.6);
    background:transparent;
    color:white;
    cursor:pointer;
    letter-spacing:3px;
    transition:0.3s;
}

button:hover{
    background:white;
    color:black;
}


.error{
    color:#ffb3b3;
    margin-bottom:15px;
    font-size:13px;
}


.link{
    margin-top:15px;
    font-size:12px;
}

.link a{
    color:white;
    text-decoration:underline;
}

</style>
</head>

<body>

<div class="form-box">

    <h1>REGISTER</h1>

    <?php if(!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-box">
            <input type="text" name="username" required>
            <label>Username</label>
        </div>

        <div class="input-box">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <div class="input-box">
            <input type="password" name="confirm_password" required>
            <label>Confirm Password</label>
        </div>

        <button type="submit" name="register">REGISTER</button>

        <div class="link">
             <a href="login.php">Login</a>
        </div>

    </form>

</div>

</body>
</html>