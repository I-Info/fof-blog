<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->blog_id) || !is_numeric($data->blog_id))
    die(http_bad_request());

$id = $data->blog_id;

$conn = db_connect();

try {
    $stmt = $conn->prepare("DELETE FROM blog_likes WHERE `uid` = ? AND `blog_id` = ?;");
    $stmt->bind_param("ii", $uid, $id);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        exit(http_not_found());
    }

    $stmt = $conn->prepare("UPDATE `blogs` SET `likes` = `likes` - 1 WHERE `id` = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        exit(http_ok());
    }
    die(http_not_found());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}