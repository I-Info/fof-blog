<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->blog_id) || !is_numeric($data->blog_id))
    die(http_bad_request());

$conn = db_connect();

if ($uid === "0") {
    $stmt = $conn->prepare("DELETE FROM `blogs` WHERE `id` = ?");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("i", $bid);

} else {
    $stmt = $conn->prepare("DELETE FROM `blogs` WHERE `id` = ? AND `uid` = ?");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("ii", $bid, $uid);
}
$bid = $data->blog_id;

try {
    if ($stmt->execute() !== false && $stmt->affected_rows > 0) {
        exit(http_ok());
    }
    exit(http_forbidden());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_server_error($DEBUG ? $exception->getMessage() : "internal server error"));
}