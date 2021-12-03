<?php
require_once "../functions.php";

debug();
global $ADMIN;

$data = parse_json();

if (!(isset($data->username) && isset($data->passwd))) {
    die(http_bad_request());
}

$stat = check_log_status();
if ($stat !== false)
    exit(http_found($stat));

// administrator auth
if ($data->username == $ADMIN['username'] && $data->passwd == $ADMIN['passwd']) {
    $_SESSION['uid'] = "0";
    exit(http_ok());
} else {
    // basic user auth
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT `id`, `passwd` FROM `users` WHERE `name` = ?");
    $stmt->bind_param("s", $name);
    $name = $data->username;
    $passwd = md5($data->passwd);
    if (!$stmt->execute())
        die(http_server_error());
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['passwd'] === $passwd) {
            $_SESSION['uid'] = $row['id'];
            exit(http_ok());
        }
    }
    exit(http_forbidden());
}
