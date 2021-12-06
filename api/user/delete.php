<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

if ($uid != 0)
    die(http_forbidden("admin auth required"));

$data = parse_json();

if (!isset($data->uid) || !is_numeric($data->uid))
    die(http_bad_request());
$id = $data->uid;

$conn = db_connect();
try {
    $conn->begin_transaction();
    $stmt = $conn->prepare("SELECT blog_id FROM blog_likes WHERE uid = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows) {
        foreach ($result as $row) {
            $stmt = $conn->prepare("UPDATE blogs SET likes = likes - 1 WHERE id = ?");
            $stmt->bind_param("i", $bid);
            $bid = $row['blog_id'];
            $stmt->execute();
        }
    }
    $stmt = $conn->prepare("SELECT comment_id FROM comment_likes WHERE uid = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows) {
        foreach ($result as $row) {
            $stmt = $conn->prepare("UPDATE comments SET likes = likes - 1 WHERE id = ?");
            $stmt->bind_param("i", $bid);
            $bid = $row['comment_id'];
            $stmt->execute();
        }
    }
    $stmt = $conn->prepare("SELECT uid FROM followers WHERE follower_uid = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows) {
        foreach ($result as $row) {
            $stmt = $conn->prepare("UPDATE users SET followers = followers - 1 WHERE id = ?");
            $stmt->bind_param("i", $bid);
            $bid = $row['uid'];
            $stmt->execute();
        }
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?;");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if ($stmt->affected_rows) {
        $conn->commit();
        exit(http_ok());
    }
    $conn->rollback();
    exit(http_not_found());
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    global $DEBUG;
    exit(http_server_error($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
}