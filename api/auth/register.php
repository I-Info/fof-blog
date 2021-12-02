<?php
require_once "../functions.php";

debug();

global $ADMIN;

$data = parse_json();

if (!(isset($data->username) && isset($data->passwd) && isset($data->email) && isset($data->tel)))
    die(http_bad_request());


$stat = check_log_status();
if ($stat !== false)
    exit(http_found($stat));

$name = $data->username;
$passwd = $data->passwd;
$email = $data->email;
$tel = $data->tel;

// check name
if (strlen($name) < 1 || strlen($name) > 15)
    die(http_bad_request("invalid username"));
if (strlen($passwd) < 5 || strlen($passwd) > 25)
    die(http_bad_request("invalid password"));
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    die(http_bad_request("invalid email"));
if (!preg_match("/^1[34578][0-9]\d{8}$/", $tel))
    die(http_bad_request("invalid tel"));

$conn = db_connect();
$stmt = $conn->prepare("INSERT INTO `users` (name, passwd, email, tel) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $passwd, $email, $tel);
$passwd = md5($passwd);
try {
    if (!$stmt->execute())
        die(http_bad_request());
    exit(http_ok());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    if ($exception->getCode() == 1062)
        exit(http_forbidden("duplicate username"));
    else
        exit(http_bad_request($DEBUG ? $exception->getMessage() : "bad request"));
}
