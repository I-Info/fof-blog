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
// positive lock
for ($i = 0; $i < 5; ++$i) {
    // mysql transaction
    if ($conn->begin_transaction() == false)
        die(http_server_error("transaction fail"));
    try {
        $stmt = $conn->prepare("SELECT `update_time` AS `t` FROM `blogs` WHERE `id` = ?;");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || !$result->num_rows) {
            die(http_not_found());
        }
        $time = $result->fetch_assoc()['t'];
        $stmt = $conn->prepare("DELETE FROM `likes` WHERE `uid` = ? AND `blog_id` = ?;");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        if ($stmt->affected_rows <= 0) {
            exit(http_not_found());
        }

        $stmt = $conn->prepare("UPDATE `blogs` SET `likes` = `likes` - 1 WHERE `id` = ? AND `update_time` = ?;");
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
        exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
    }
}
echo http_server_error("time out");