<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

if (!isset($data->uid) || !is_numeric($data->uid))
    die(http_bad_request());

$id = $data->uid; // the user to be followed

$conn = db_connect();
// positive lock
try {
    $stmt = $conn->prepare("DELETE FROM `followers` WHERE `uid` = ? AND `follower_uid` = ?;");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
        exit(http_not_found());
    }

    $stmt = $conn->prepare("UPDATE `users` SET `followers` = `followers` - 1 WHERE `id` = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows) {
        exit(http_ok());
    }
    die(http_server_error());
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    global $DEBUG;
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}
