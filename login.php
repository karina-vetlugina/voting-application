<?php
session_start();
require_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    authenticateUser($username, $password);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Voting App!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }

        .login-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 320px;
            margin-bottom: 15px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: inline-block;
            text-align: left;
            width: 100%;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        p {
            font-size: 13px;
            margin-top: 20px;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .show-source {
            font-size: 18px;
            margin-top: 15px;
            text-align: center;
        }

        .show-source a {
            color: #6a0dad;
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.3s;
        }

        .show-source a:hover {
            color: #9a4dff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-box">
        <h2>Login</h2>

        <!-- Display error message -->
        <?php
        if ($error = getSession('error')) {
            echo '<p style="color: red;">'.htmlspecialchars($error).'</p>';
            setSession('error', null); // Clear the error message after displaying it
        }
        ?>

        <form method="POST" action="login.php">
            <label for="username">Username: </label><br>
            <input type="text" name="username" placeholder="Enter your username: " required><br>
            <label for="password">Password: </label><br>
            <input type="password" name="password" placeholder="Enter your password: " required><br>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account?<a href="register.php"> Register here</a></p>
    </div>

    <!-- Show Source Link -->
    <p class="show-source"><a href="show.php?file=<?= urlencode(__FILE__); ?>" target="_blank">Show Source</a></p>
</div>
</body>
</html>