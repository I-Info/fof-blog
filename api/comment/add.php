<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->content) || strlen($data->content) < 1 || strlen($data->content) > 250)
    exit(http_bad_request());
if (!isset($data->blog_id) || !is_numeric($data->blog_id))
    die(http_bad_request());

$conn = db_connect();
$stmt = $conn->prepare("INSERT INTO `comments` (`content`, `uid`, `blog_id`) VALUES (?, ?, ?)");
if ($stmt === false)
    die(http_server_error());

$stmt->bind_param("sii", $content, $uid, $blog_id);
$blog_id = $data->blog_id;
$content = $data->content;
try {
    $stmt->execute();
    if ($stmt->affected_rows)
        exit(http_ok());
    exit(http_bad_request());
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() == 1452)
        exit(http_not_found());
    global $DEBUG;
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}