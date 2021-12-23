<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->id) || !is_numeric($data->id))
    die(http_bad_request());

$conn = db_connect();
if ($uid === "0") {
    $stmt = $conn->prepare("DELETE FROM `comments` WHERE id = ?;");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("DELETE FROM `comments` WHERE id = ? AND uid = ?;");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("ii", $id, $uid);
}
$id = $data->id;

try {
    $stmt->execute();
    if ($stmt->affected_rows)
        exit(http_ok());
    exit(http_not_found());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}