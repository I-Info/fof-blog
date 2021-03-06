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
    $stmt = $conn->prepare("INSERT INTO comment_likes (`uid`, `comment_id`) VALUES (?, ?);");
    $stmt->bind_param("ii", $uid, $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE `comments` SET `likes` = `likes` + 1 WHERE `id` = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        exit(http_ok());
    }
    die(http_not_found());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    if ($exception->getCode() == 1062)
        exit(http_forbidden("already liked"));
    if ($exception->getCode() == 1452)
        exit(http_not_found());
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}
