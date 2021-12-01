<?php
require_once "../functions.php";

debug();
global $ADMIN;

$data = parse_json();

if (!(isset($data->username) && isset($data->passwd))) {
    die(status_bad_request());
}

session_start();
// already logged in
if (isset($_SESSION['uid'])) {
    exit(status_found($_SESSION['uid']));
}

// administrator auth
if ($data->username == $ADMIN['username'] && $data->passwd == $ADMIN['passwd']) {
    $_SESSION['uid'] = "admin";
    exit(status_ok());
} else {
    // basic user auth
    $conn = mysql_connect();
    $stmt = $conn->prepare("SELECT `uid` FROM `users` WHERE `name` = ? AND `passwd` = ?");
    $stmt->bind_param("ss", $name, $passwd);
    $name = $data->username;
    $passwd = md5($data->passwd);
    if (!$stmt->execute())
        die(status_server_error());
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $_SESSION['uid'] = $row['uid'];
        exit(status_ok());
    }
    exit(status_forbidden());
}
