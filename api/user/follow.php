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
for ($i = 0; $i < 5; ++$i) {
    // mysql transaction
    if ($conn->begin_transaction() == false)
        die(http_server_error("transaction fail"));
    try {
        $stmt = $conn->prepare("SELECT `update_time` AS `t` FROM `users` WHERE `id` = ?;");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || !$result->num_rows) {
            die(http_not_found());
        }
        $time = $result->fetch_assoc()['t'];

        $stmt = $conn->prepare("INSERT INTO `followers` (`uid`, `follower_uid`) VALUES (?, ?);");
        $stmt->bind_param("ii", $id, $uid);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE `users` SET `followers` = `followers` + 1 WHERE `id` = ? AND `update_time` = ?;");
        $stmt->bind_param("is", $id, $time);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $conn->commit();
            exit(http_ok());
        } else {
            $conn->rollback();
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        global $DEBUG;
        if ($exception->getCode() == 1062)
            exit(http_forbidden("already followed"));
        exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
    }
}
echo http_server_error("time out");