<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

$name = $data->username ?? null;
$passwd = $data->passwd ?? null;
$email = $data->email ?? null;
$tel = $data->tel ?? null;

// check name
if ($name && (strlen($name) < 1 || strlen($name) > 15))
    die(http_bad_request("invalid username"));
if ($passwd && (strlen($passwd) < 5 || strlen($passwd) > 25))
    die(http_bad_request("invalid password"));
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
    die(http_bad_request("invalid email"));
if ($tel && !preg_match("/^1[34578][0-9]\d{8}$/", $tel))
    die(http_bad_request("invalid tel"));

$conn = db_connect();
$sql = "UPDATE users SET";
$params = array();
$flag = 0x0;
$type = "";
if ($name) {
    $sql .= " name = ?";
    $params[] = $name;
    $flag += 1;
    $type .= "s";
}
if ($passwd) {
    $sql .= ($flag ? "," : "") . " passwd = ?";
    $params[] = md5($passwd);
    $flag += 1;
    $type .= "s";
}
if ($email) {
    $sql .= ($flag ? "," : "") . " email = ?";
    $params[] = $email;
    $flag += 1;
    $type .= "s";
}
if ($tel) {
    $sql .= ($flag ? "," : "") . " tel = ?";
    $params[] = $tel;
    $flag += 1;
    $type .= "s";
}
if (!$flag) {
    die(http_bad_request());
}
if ($uid == 0) {
    if (!isset($data->uid) || is_numeric($data->uid))
        die(http_bad_request());
    $sql .= " WHERE id = ?";
    $params[] = (int)$data->uid;
} else {
    $sql .= " WHERE id = ?";
    $params[] = (int)$uid;
}
$type .= "i";
$result = query_prepared($conn, $sql, $params);
if ($result != false)
    exit(http_ok());
die(http_not_found());