<?php
include 'inc/header.php';
login_check_pages();
?>

<h1>Login</h1>

<div>
    <?php
    display_message();
    validate_user_login();


    ?>
</div>


<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login-submit" value="log in">
</form>


<?php include "inc/footer.php"; ?>