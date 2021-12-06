<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

$conn = db_connect();
$stmt = $conn->prepare("SELECT uid FROM followers WHERE follower_uid = ?;");
if (isset($data->uid)) {
    $stmt->bind_param("i", $id);
    $id = $data->uid;
} else {
    // current user
    $stmt->bind_param("i", $uid);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows) {
    $r = array();
    foreach ($result as $row) {
        $r[] = $row['uid'];
    }
    exit(http_ok("ok", $r));
}
die(http_not_found());