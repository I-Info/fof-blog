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
if ($id == $uid)
    die(http_forbidden("cannot follow yourself"));

$conn = db_connect();

try {
    $stmt = $conn->prepare("INSERT INTO `followers` (`uid`, `follower_uid`) VALUES (?, ?);");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE `users` SET `followers` = `followers` + 1 WHERE `id` = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        exit(http_ok());
    }
    exit(http_not_found());
} catch (mysqli_sql_exception $exception) {
    global $DEBUG;
    if ($exception->getCode() == 1062)
        exit(http_forbidden("already followed"));
    if ($exception->getCode() == 1452)
        exit(http_not_found());
    exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}

