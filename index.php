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
            align-items: center;
        }

        .center-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            padding-top: 100px;
        }

        .welcome-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 10px;
        }

        .welcome-box h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .welcome-box p {
            font-size: 16px;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 5px;
        }

        .btn:hover {
            background-color: #0056b3;
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
<div class="center-container">
    <div class="welcome-box">
        <h2>Welcome to Voting App!</h2>
        <p>Please<a href="login.php" class="btn">Login</a> to continue.</p>
        <p>Don't have an account?<a href="register.php" class="btn">Register</a></p>
    </div>

    <!-- Show Source Link -->
    <p class="show-source"><a href="show.php?file=<?= urlencode(__FILE__); ?>" target="_blank">Show Source</a></p>
</div>
</body>
</html>