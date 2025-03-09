<?php
// Determine file path based on environment
$filePathPrefix = (php_sapi_name() === 'cli') ? 'tests/' : '';

// Registers a new user by storing the username and password in the users.txt file
function registerUser($username, $password)
{
    global $filePathPrefix;
    $filePath = $filePathPrefix.'users.txt';

    if (!file_exists($filePath)) {
        $file = fopen($filePath, "a"); // Create the file if it doesn't exist
        if ($file === false) {
            setSession('error', "Unable to create users file!");
            if (php_sapi_name() !== 'cli') { // Only redirect in non-CLI environments
                header("Location: register.php");
                exit();
            }
            return false;
        }
        chmod($filePath, 0666);
        fclose($file);
    }

    $file = fopen($filePath, "r"); // Open the file for reading
    if ($file === false) {
        setSession('error', "Unable to read users file!");
        if (php_sapi_name() !== 'cli') {
            header("Location: register.php");
            exit();
        }
        return false;
    }

    // Check if the username already exists
    while (($userData = fgetcsv($file, 0, ":")) !== false) {
        if ($userData[0] == $username) {
            setSession('error', "Username already exists!");
            fclose($file);
            if (php_sapi_name() !== 'cli') {
                header("Location: register.php");
                exit();
            }
            return false;
        }
    }
    fclose($file);

    // Open the file to add a new user
    $file = fopen($filePath, "a");
    if ($file === false) {
        setSession('error', "Unable to open users file for writing!");
        if (php_sapi_name() !== 'cli') {
            header("Location: register.php");
            exit();
        }
        return false;
    }

    // Write the new user data
    $newUserEntry = $username.":".$password."\n";
    if (fwrite($file, $newUserEntry) === false) {
        setSession('error', "Unable to write to users file!");
        fclose($file);
        return false;
    }
    fclose($file);
    setSession('success', "Registration successful!");
    if (php_sapi_name() !== 'cli') {
        header("Location: register.php");
        exit();
    }
    return true;
}

// Authenticates a user by checking the users.txt file for matching credentials
function authenticateUser($username, $password)
{
    global $filePathPrefix;
    $filePath = $filePathPrefix.'users.txt';

    if (!file_exists($filePath)) {
        setSession('error', "User file not found!");
        if (php_sapi_name() !== 'cli') {
            header("Location: login.php");
            exit();
        }
        return false;
    }

    $file = fopen($filePath, "r"); // Open the file for reading
    if ($file === false) {
        setSession('error', "Unable to read users file!");
        if (php_sapi_name() !== 'cli') {
            header("Location: login.php");
            exit();
        }
        return false;
    }

    while (($userData = fgetcsv($file, 0, ":")) !== false) {
        if ($userData[0] == $username && $userData[1] == $password) {
            setSession('username', $username);
            fclose($file);
            if (php_sapi_name() !== 'cli') {
                header("Location: create_topic.php");
                exit();
            }
            return true;
        }
    }
    fclose($file);
    setSession('error', "Wrong username or password! Please try again.");
    if (php_sapi_name() !== 'cli') {
        header("Location: login.php");
        exit();
    }
    return false;
}

// Generates a unique id for one record
function getID() // Modified function from appendix
{
    global $filePathPrefix;
    $filePath = $filePathPrefix.'ids';

    if (!file_exists($filePath)) {
        touch($filePath);
        chmod($filePath, 0666);
        $handle = fopen($filePath, 'r+');
        $id = 0;
    }
    else {
        $handle = fopen($filePath, 'r+');
        if (filesize($filePath) > 0) {
            $id = fread($handle, filesize($filePath));
            settype($id, "integer"); // Convert to integer
        } else {
            $id = 0; // Initialize ID if the file is empty
        }
    }
    rewind($handle);
    fwrite($handle, ++$id);
    fclose($handle);
    return $id;
}

// Creates a new topic and stores it in topics.txt
function createTopic($username, $title, $description)
{
    global $filePathPrefix;
    $filePath = $filePathPrefix.'topics.txt';

    if (!file_exists($filePath)) {
        $file = fopen($filePath, "a"); // Create the file if it doesn't exist
        if ($file === false) {
            setSession('error', "Unable to create topics file!");
            if (php_sapi_name() !== 'cli') {
                header("Location: create_topic.php");
                exit();
            }
            return false;
        }
        chmod($filePath, 0666);
        fclose($file);
    }

    // Generate a new unique topic ID
    $id = getID();

    // Open the file to add a new topics
    $file = fopen($filePath, "a");
    if ($file === false) {
        setSession('error', "Unable to open topics file for writing!");
        if (php_sapi_name() !== 'cli') {
            header("Location: create_topic.php");
            exit();
        }
        return false;
    }

    // Write the new topic data
    $newTopicEntry = $id."|".$username."|".$title."|".$description."\n";
    if (fwrite($file, $newTopicEntry) === false) {
        setSession('error', "Unable to write to topics file!");
        fclose($file);
        return false;
    }
    fclose($file);
    if (php_sapi_name() !== 'cli') {
        header("Location: vote.php");
        exit();
    }
    return true;
}

// Retrieves all topics stored in topics.txt
function getTopics()
{
    global $filePathPrefix;
    $filePath = $filePathPrefix.'topics.txt';

    $topics = [];
    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($topicData = fgetcsv($file, 0, "|")) !== false) {
                if (count($topicData) === 4) {
                    $topics[] = [
                        "topicID" => $topicData[0],
                        "creator" => $topicData[1],
                        "title" => $topicData[2],
                        "description" => $topicData[3],
                    ];
                }
            }
            fclose($file);
        }
    }
    return $topics;
}

// Casts a vote (up or down) for a topic
function vote($username, $topicID, $voteType) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'votes.txt';

    if (hasVoted($username, $topicID)) {
        setSession('error', "You have already voted on this topic!");
        if (php_sapi_name() !== 'cli') {
            header("Location: vote.php");
            exit();
        }
        return false;
    }

    if (!file_exists($filePath)) {
        $file = fopen($filePath, "a"); // Create the file if it doesn't exist
        if ($file === false) {
            setSession('error', "Unable to create votes file!");
            if (php_sapi_name() !== 'cli') {
                header("Location: vote.php");
                exit();
            }
            return false;
        }
        chmod($filePath, 0666);
        fclose($file);
    }

    // Open the file to add a new topics
    $file = fopen($filePath, "a");
    if ($file === false) {
        setSession('error', "Unable to open votes file for writing!");
        if (php_sapi_name() !== 'cli') {
            header("Location: vote.php");
            exit();
        }
        return false;
    }

    // Optional: Use flock() to lock the file
    if (flock($file, LOCK_EX)) { // acquire an exclusive lock
        if (fwrite($file, $username."|".$topicID."|".$voteType."\n") === false) {
            setSession('error', "Unable to write to votes file!");
            fclose($file);
            return false;
        }
        flock($file, LOCK_UN); // release the lock after writing
    } else {
        setSession('error', "Unable to lock the votes file!");
        fclose($file);
        if (php_sapi_name() !== 'cli') {
            header("Location: vote.php");
            exit();
        }
        return false;
    }
    fclose($file);
    if (php_sapi_name() !== 'cli') {
        header("Location: vote.php");
        exit();
    }
    return true;
}

// Checks if a user has already voted on a given topic
function hasVoted($username, $topicID) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'votes.txt';

    if (!file_exists($filePath)) {
        return false; // user hasn't voted
    }

    $file = fopen($filePath, "r");
    if ($file === false) {
        setSession('error', "Unable to read votes file!");
        return false;
    }

    while (($data = fgetcsv($file, 0, "|")) !== false) {
        if (count($data) >= 3) {
            $voter = $data[0];
            $votedTopicID = $data[1];

            if ($voter === $username && $votedTopicID == $topicID) {
                fclose($file);
                return true; // user has already voted on this topic
            }
        }
    }
    fclose($file);
    return false; // user hasn't voted on this topic
}

// Retrieves the total number of upvotes and downvotes for a topic
function getVoteResults($topicID) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'votes.txt';

    $results = [
        'up' => 0,
        'down' => 0
    ];

    if (!file_exists($filePath)) {
        return $results;
    }

    $file = fopen($filePath, "r");
    if ($file === false) {
        setSession('error', "Unable to open votes file for reading!");
        if (php_sapi_name() !== 'cli') {
            header("Location: vote.php");
            exit();
        }
        return $results;
    }

    while (($data = fgetcsv($file, 0, "|")) !== false) {
        if (count($data) < 3) {
            continue;
        }

        list($voter, $votedTopicID, $voteType) = $data;

        if ($votedTopicID == $topicID) {
            if ($voteType === "up") {
                $results['up']++;
            } elseif ($voteType === "down") {
                $results['down']++;
            }
        }
    }
    fclose($file);
    return $results;
}

// Optional: Lists the top-voted topics based on upvotes
function getLeaderboard() {
    $topics = getTopics();
    $leaderboard = [];

    foreach ($topics as $topic) {
        $voteResults = getVoteResults($topic['topicID']);
        $topic['upvotes'] = $voteResults['up'];
        $leaderboard[] = $topic;
    }

    usort($leaderboard, function($a, $b) { // sort in desc order
        return $b['upvotes'] - $a['upvotes'];
    });
    return $leaderboard;
}

// Retrieves all topics created by a specific user
function getUserCreatedTopics($username) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'topics.txt';

    $userTopics = [];

    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($topicData = fgetcsv($file, 0, "|")) !== false) {
                if (count($topicData) === 4) {
                    list($topicID, $creator, $title, $description) = $topicData;
                    if ($creator === $username) {
                        $userTopics[] = [
                            "topicID" => $topicID,
                            "creator" => $creator,
                            "title" => $title,
                            "description" => $description
                        ];
                    }
                }
            }
            fclose($file);
        }
    }
    return $userTopics;
}

// Returns an array of topics that the user has voted on, and the vote type (up or down)
function getUserVotingHistory($username) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'votes.txt';

    $votingHistory = [];

    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($voteData = fgetcsv($file, 0, "|")) !== false) {
                if (count($voteData) === 3) {
                    list($voter, $topicID, $voteType) = $voteData;
                    if ($voter === $username) {
                        $topic = getTopicById($topicID);
                        if ($topic) {
                            $topic['voteType'] = $voteType;
                            $votingHistory[] = $topic;
                        }
                    }
                }
            }
            fclose($file);
        }
    }
    return $votingHistory;
}

// Helper function (retrieves a topic by its topicID from topics.txt)
function getTopicById($topicID) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'topics.txt';

    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($topicData = fgetcsv($file, 0, "|")) !== false) {
                if (count($topicData) === 4) {
                    list($id, $creator, $title, $description) = $topicData;
                    if ($id == $topicID) {
                        fclose($file);
                        return [
                            "topicID" => $id,
                            "creator" => $creator,
                            "title" => $title,
                            "description" => $description
                        ];
                    }
                }
            }
            fclose($file);
        }
    }
    return null;
}

// Calculates the total number of topics created by the specified user
function getTotalTopicsCreated($username) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'topics.txt';

    $count = 0;

    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($topicData = fgetcsv($file, 0, "|")) !== false) {
                if (count($topicData) === 4) {
                    $creator = $topicData[1];
                    if ($creator === $username) {
                        $count++;
                    }
                }
            }
            fclose($file);
        }
    }
    return $count;
}

// Calculates the total number of votes cast by the specified user
function getTotalVotesCast($username) {
    global $filePathPrefix;
    $filePath = $filePathPrefix.'votes.txt';

    $count = 0;

    if (file_exists($filePath)) {
        $file = fopen($filePath, "r");

        if ($file) {
            while (($voteData = fgetcsv($file, 0, "|")) !== false) {
                if (count($voteData) === 3) {
                    $voter = $voteData[0];
                    if ($voter === $username) {
                        $count++;
                    }
                }
            }
            fclose($file);
        }
    }
    return $count;
}

// Sets a cookie with a given key and value
function set_cookie($key, $value) {
    // Check if running in CLI (command line) mode
    if (php_sapi_name() == "cli") { // Directly set $_COOKIE for testing in CLI
        $_COOKIE[$key] = $value;
    } else {
        // Use setcookie() for browser environment
        return setcookie($key, $value, time() + (86400 * 30), "/"); // 30 days expiry
    }
    return true;
}

// Retrieves a cookie value
function getCookie($key) {
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
}

// Sets the theme preference using a cookie
function setTheme($username, $theme) {
    setSession("theme_$username", $theme);
    set_cookie("theme_$username", $theme);
}

// Gets the current theme preference using the getCookie function
function getTheme($username) {
    $theme = getSession("theme_$username");

    if ($theme === null) {
        $theme = getCookie("theme_$username") ? getCookie("theme_$username") : 'light';
    }
    return $theme;
}

// Sets a session variable
function setSession($key, $value) {
    $_SESSION[$key] = $value;
    return true;
}

// Retrieves a session variable
function getSession($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

if (isset($_GET['show_source']) && $_GET['show_source'] === 'true') {
    show_source(__FILE__);
}
