<!DOCTYPE html>
<html lang="de">
<head>
    <title>Get_Post</title>
</head>

<body>
<h2>Get</h2>
<form action="get_post.php" method="get"> <!--action  ist wo er die daten senden soll || method ist die art wie sie versendet wird -->
    <label>username</label><br>
    <input type="text" name="username"><br>
    <label>passwort</label><br>
    <input type="password" name="password"><br>
    <input type="submit" value="login">
</form>


<h2>Post</h2>
<form action="get_post.php" method="post"> <!--action  ist wo er die daten senden soll || method ist die art wie sie versendet wird -->
    <label>username</label><br>
    <input type="text" name="username"><br>
    <label>passwort</label><br>
    <input type="password" name="password"><br>
    <input type="submit" value="login">
</form>
</body>
</html>

<?php
// $_GET and  $_POST sind supervariablen
// verwendung : sie werden verwendeet um daten von einem formular zu erhalten oder zu senden

echo $_GET["username"] . "<br>";
echo $_GET["password"];

echo $_POST["username"] . "<br>";
echo $_POST["password"];
?>



