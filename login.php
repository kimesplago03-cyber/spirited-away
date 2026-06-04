<?php
session_start();

$error = '';
$loginSuccess = false;

if (isset($_POST['login'])) {

    require_once 'conn.php';

    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = hash('sha256', $_POST['password']);

    $sql = "SELECT * FROM $tablelogin
            WHERE username='$username'
            AND password='$password'";

    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) == 1) {

        $_SESSION['username'] = $username;
        $loginSuccess = true;

    } else {
        $error = "Invalid username or password.";
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="icon" type="image/png" href="img/tabicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Yuji+Mai&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:url('loginphoto.jpg') center center/cover no-repeat fixed;
    font-family: 'Yuji Mai', serif;
    overflow:hidden;
}

/* Fade overlay */
#fadeOverlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:black;
    opacity:0;
    visibility:hidden;
    transition:0.8s ease;
    z-index:9999;
}

#fadeOverlay.active{
    opacity:1;
    visibility:visible;
}

/* LOGIN CARD */
.login-card{
    width:360px;
    text-align:center;
    color:white;
}

.login-card h2{
    font-size:48px;
    letter-spacing:8px;
    margin-bottom:30px;
    font-family:'Yuji Mai', serif;
    color:#fff;
    text-shadow:
        0 0 10px rgba(255,255,255,0.25),
        0 0 25px rgba(255,255,255,0.15);
}
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
    font-size:15px;
    font-family: 'Yuji Mai', serif;
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

/* BUTTON */
button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid rgba(255,255,255,0.6);
    background:transparent;
    color:white;
    cursor:pointer;
    letter-spacing:4px;
    font-family:'Yuji Mai', serif;
    transition:0.3s;
}

button:hover{
    background:white;
    color:black;
}


.error{
    color:#ffb3b3;
    font-size:13px;
    margin-bottom:15px;
}


.link{
    margin-top:15px;
    font-size:12px;
    color:white;
}

.link a{
    color:white;
    text-decoration:underline;
}

body{
    animation:fadeIn 1s ease;
}

@keyframes fadeIn{
    from{opacity:0;}
    to{opacity:1;}
}

</style>
</head>

<body>

<div id="fadeOverlay"></div>

<div class="login-card">

    <h2>LOGIN</h2>

    <?php if(!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">

        <div class="input-box">
            <input type="text" name="username" required>
            <label>Username</label>
        </div>

        <div class="input-box">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <button type="submit" name="login">LOGIN</button>

        <div class="link">
             <a href="register.php">Create Account</a>
        </div>

    </form>

</div>

<?php if($loginSuccess): ?>
<script>
window.onload = function(){
    const overlay = document.getElementById('fadeOverlay');
    overlay.classList.add('active');

    setTimeout(() => {
        window.location.href = "index.php";
    }, 800);
};
</script>
<?php endif; ?>

</body>
</html>