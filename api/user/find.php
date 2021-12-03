<?php
require_once "../functions.php";

debug();

$data = parse_json();
if (!isset($data->username) || strlen($data->username) < 1 || strlen($data->username) > 15)
    die(http_bad_request());

$name = $data->username;
global $ADMIN;
if ($name === $ADMIN['username'])
    exit(http_ok("ok", array("uid" => 0)));

$conn = db_connect();
$stmt = $conn->prepare("SELECT `id` FROM `users` WHERE `name` = ?;");
if ($stmt === false)
    die(http_server_error());
$stmt->bind_param("s", $name);
try {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        exit(http_ok("ok", array("uid" => $result->fetch_assoc()['id'])));
    }
    exit(http_not_found());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_bad_request($DEBUG ? $exception->getMessage() : "bad request"));
}
