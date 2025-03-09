<?php
session_start();
require_once "functions.php";

// Check if the user is logged in; if not, redirect to login page
if (!getSession('username')) {
    if (php_sapi_name() !== 'cli') {
        header("Location: login.php");
        exit();
    }
}

// Get the logged-in user's username
$username = getSession("username");

$userTopics = getUserCreatedTopics($username);
$userVotes = getUserVotingHistory($username);
$totalTopicsCreated = getTotalTopicsCreated($username);
$totalVotesCast = getTotalVotesCast($username);

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
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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

    .container {
        width: 100%;
        max-width: 1200px;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        border-radius: 8px;
    }

    .dark-light-mode {
        float: right;
    }

    .stat-list {
        list-style-type: none;
        margin-bottom: 20px;
    }

    .stat-list li {
        font-size: 18px;
        margin: 5px 0;
    }

    .vote-history {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .vote-history h4 {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .vote-history p {
        margin: 0;
        font-size: 14px;
        color: #555;
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
        color: #e0e0e0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    body.dark .container h3 {
        color: #f5f5f5;
    }

    body.dark .stat-list li {
        color: #007bff;
    }

    body.dark .vote-history {
        background-color: #2b2b2b;
        color: #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    body.dark .vote-history h4 {
        color: #e0e0e0;
    }

    body.dark .vote-history p {
        color: #b3b3b3;
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
    <h1><?php echo strtoupper($username); ?>'s Profile</h1><br>
    <p>Here you can see the history of your votes and topics</p>
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
    <ul class="stat-list">
        <li style="font-weight: bold; color: #007bff;">Total Topics Created: <?php echo count($userTopics); ?></li>
        <li style="font-weight: bold; color: #007bff;">Total Votes Cast: <?php echo count($userVotes); ?></li>
    </ul>

    <h3>Here is the list of topics you have voted on:</h3><br>
    <?php if (!empty($userVotes)): ?>
        <?php foreach ($userVotes as $vote): ?>
            <div class="vote-history">
                <h4>Title: <?php echo htmlspecialchars($vote['title']); ?></h4>
                <p><?php echo htmlspecialchars($vote['description']); ?> | Vote: <?php echo htmlspecialchars($vote['voteType']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not voted on any topics yet.</p>
    <?php endif; ?>
</div>

<!-- Show Source Link -->
<p class="show-source"><a href="show.php?file=<?= urlencode(__FILE__); ?>" target="_blank">Show Source</a></p>
</body>
</html>