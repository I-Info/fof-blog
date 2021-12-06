<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->id) || !is_numeric($data->id))
    die(http_bad_request());

$id = $data->id;

$conn = db_connect();

try {
    $stmt = $conn->prepare("DELETE FROM comment_likes WHERE `uid` = ? AND `comment_id` = ?;");
    $stmt->bind_param("ii", $uid, $id);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        exit(http_not_found());
    }

    $stmt = $conn->prepare("UPDATE `comments` SET `likes` = `likes` - 1 WHERE `id` = ?;");
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