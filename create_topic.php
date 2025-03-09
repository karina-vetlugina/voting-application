<?php
session_start();
require_once "functions.php";

// Check if the user is logged in, if not redirect to login page
if (!getSession('username')) {
    if (php_sapi_name() !== 'cli') {
        header("Location: login.php");
        exit();
    }
}

$username = getSession("username");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];

    createTopic($username, $title, $description);
}

// Check if the user clicked the logout link
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    if (php_sapi_name() !== 'cli') {
        header("Location: login.php");
        exit();
    }
}

// Set theme cookie and refresh page
if (isset($_GET['theme'])) {
    $username = getSession("username");
    setTheme($username, $_GET['theme']);
    if (php_sapi_name() !== 'cli') {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get the saved theme
$theme = getTheme($username);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to Voting App!</title>
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
        font-family: Arial, sans-serif;
    }

    body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    header {
        width: 100%;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    nav {
        margin: 20px 0;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 1200px;
        display: flex;
        justify-content: space-around;
    }

    nav a {
        color: #333;
        text-decoration: none;
        padding: 10px 20px;
        font-weight: bold;
    }

    nav a:hover {
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
    }

    .dark-light-mode {
        float: right;
    }

    .container {
        width: 100%;
        max-width: 1200px;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        border-radius: 8px;
    }

    .container h3 {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    textarea {
        resize: none;
    }

    .submit-btn {
        background-color: #007BFF;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .submit-btn:hover {
        background-color: #0056b3;
    }

    input::placeholder,
    textarea::placeholder {
        font-family: Arial, sans-serif;
        font-size: 16px;
        opacity: 1;
    }

    input, textarea {
        font-family: Arial, sans-serif;
        font-size: 16px;
    }

    body.light {
        background-color: #f2f2f2;;
    }

    body.dark {
        background-color: #121212;
        color: #e0e0e0;
    }

    body.dark header {
        background-color: #1f1f1f;
        color: #e0e0e0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    body.dark nav {
        background-color: #1c1c1c;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    body.dark nav a {
        color: #b3b3b3;
    }

    body.dark nav a:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    body.dark .container {
        background-color: #1e1e1e;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    body.dark .container h3 {
        color: #f5f5f5;
    }

    body.dark input[type="text"],
    body.dark textarea {
        background-color: #2a2a2a;
        border: 1px solid #555;
        color: #e0e0e0;
    }

    body.dark input::placeholder,
    body.dark textarea::placeholder {
        color: #888;
    }

    body.dark .submit-btn {
        background-color: #4a90e2;
    }

    body.dark .submit-btn:hover {
        background-color: #357ab8;
    }

    .dark-light-mode {
        text-align: center;
        font-size: 18px;
        margin-top: 10px;
    }

    .dark-light-mode a {
        color: #6a0dad;
        text-decoration: underline;
        margin: 0 5px;
        cursor: pointer;
        transition: color 0.3s;
    }

    .dark-light-mode a:hover {
        color: #9a4dff;
    }

    body.dark .dark-light-mode {
        color: #e0e0e0;
    }

    body.dark .dark-light-mode a {
        color: #9a4dff;
    }

    .show-source {
        text-align: center;
        font-size: 18px;
        margin-top: 10px;
    }

    .show-source a {
        color: #6a0dad;
        text-decoration: underline;
        margin: 0 5px;
        cursor: pointer;
        transition: color 0.3s;
    }

    .show-source a:hover {
        color: #9a4dff;
    }

    body.dark .show-source {
        color: #e0e0e0;
    }

    body.dark .show-source a {
        color: #9a4dff;
    }
</style>
<body class="<?php echo htmlspecialchars($theme); ?>">
<header>
    <h1>Welcome to Voting App!</h1><br>
    <p>What would you like to do?</p>
    <div class="dark-light-mode">
        <a href="?theme=dark">Dark</a> | <a href="?theme=light">Light</a>
    </div>
</header>

<nav>
    <a href="create_topic.php">Dashboard</a>
    <a href="vote.php">Topics</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="profile.php">Profile</a>
    <a href="?action=logout">Logout</a>
</nav>

<div class="container">
    <h3>Create a Topic</h3>

    <!-- Display error message -->
    <?php
    if ($error = getSession('error')) {
        echo '<p style="color: red;">'.htmlspecialchars($error).'</p>';
        setSession('error', null);
    }
    ?>

    <form method="POST" action="create_topic.php">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter the topic title: " required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5" placeholder="Enter the topic description: " required></textarea>

        <input type="submit" class="submit-btn" value="Create Topic">
    </form>
</div>

<!-- Show Source Link -->
<p class="show-source"><a href="show.php?file=<?= urlencode(__FILE__); ?>" target="_blank">Show Source</a></p>
</body>
</html>

