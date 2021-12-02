<?php
require_once "../functions.php";

$uid = check_log_status();
if (!$uid)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->content) || strlen($data->content) < 2 || strlen($data->content) > 500)
    exit(http_bad_request());

if (!isset($data->blog_id) || empty($data->blog_id))
    die(http_bad_request());


$conn = db_connect();
$stmt = $conn->prepare("UPDATE `blogs` SET `content` = ? WHERE `id` = ?;");
if ($stmt === false)
    die(http_server_error());

$stmt->bind_param("si", $content, $id);
$content = $data->content;
$id = $data->blog_id;

try {
    if (!$stmt->execute())
        die(http_bad_request());
    exit(http_ok());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_bad_request($DEBUG ? $exception->getMessage() : "bad request"));
}