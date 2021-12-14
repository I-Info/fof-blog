<?php
require_once "../functions.php";

debug();

$uid = check_log_status();


$data = parse_json();

$conn = db_connect();
if (isset($data->uid)) {
    if (!is_numeric($data->uid))
        die(http_bad_request());
    if ($uid === "0") {
        $stmt = $conn->prepare("SELECT name, followers, email, tel, create_time, update_time FROM users WHERE id = ?;");
    } else {
        $stmt = $conn->prepare("SELECT name, followers, create_time FROM users WHERE id = ?;");
    }
    $stmt->bind_param("i", $id);
    $id = $data->uid;
} else {
    // current user
    if ($uid === false)
        die(http_unauthorized());
    $stmt = $conn->prepare("SELECT name, followers, email, tel, create_time FROM users WHERE id = ?;");
    $stmt->bind_param("i", $uid);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows) {
    exit(http_ok("ok", $result->fetch_assoc()));
}
die(http_not_found());