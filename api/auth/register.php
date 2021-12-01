<?php
require_once "../functions.php";

debug();

global $ADMIN;

$data = parse_json();

if (!(isset($data->username) && isset($data->passwd) && isset($data->email) && isset($data->tel)))
    die(status_bad_request());

// already logged in
session_start();
if (isset($_SESSION['uid'])) {
    exit(status_not_allowed($_SESSION['uid']));
}

$name = $data->username;
$passwd = $data->passwd;
$email = $data->email;
$tel = $data->tel;

// check name
if (strlen($name) < 4 || strlen($name) > 15)
    die(status_bad_request("invalid username"));
if (strlen($passwd) < 8 || strlen($passwd) > 25)
    die(status_bad_request("invalid password"));
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(status_bad_request("invalid email"));
if (!preg_match("/^1[3|4|5|8][0-9]\d{8}$/", $tel))
    die(status_bad_request("invalid tel"));

$conn = mysql_connect();
$stmt = $conn->prepare("INSERT INTO `users` (name, passwd, email, tel) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $passwd, $email, $tel);
$passwd = md5($passwd);
try {
    if (!$stmt->execute())
        die(status_server_error());
    exit(status_ok());
} catch (\mysqli_sql_exception $exception) {
    global $DEBUG;
    if ($exception->getCode() == 1062)
        exit(status_forbidden("duplicate username"));
    else
        exit(status_forbidden($DEBUG ? $exception->getMessage() : "bad request"));
}
