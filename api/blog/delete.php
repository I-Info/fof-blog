<?php

require_once "../functions.php";

$uid = check_log_status();
if (!$uid)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->blog_id) || empty($data->blog_id))
    die(http_bad_request());

$conn = db_connect();
$stmt = $conn->prepare("DELETE FROM `blogs` WHERE `id` = ? AND `uid` = ?");
if ($stmt === false)
    die(http_server_error());

$stmt->bind_param("si", $bid, $uid);
$bid = $data->blog_id;

try {
    if ($stmt->execute() !== false) {
        exit(http_ok());
    }
    exit(http_bad_request());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_server_error($DEBUG ? $exception->getMessage() : "internal server error"));
}