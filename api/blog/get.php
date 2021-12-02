<?php
require_once "../functions.php";

debug();

$uid = check_log_status();
if ($uid === false)
    die(http_unauthorized());

$data = parse_json();

// page and limit
$page = isset($data->page) && $data->page >= 0 ? (int)$data->page : 0;
$limit = isset($data->limit) && $data->limit >= 1 && $data->limit <= 100 ? (int)$data->limit : 50;

if (isset($data->blog_id)) {
    if (empty($data->blog_id))
        die(http_bad_request());
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT `content`, `uid`, `likes`, `create_time`, `update_time` FROM `blogs` WHERE `id` = ?;");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("iii", $id);
    $id = $data->blog_id;
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        exit(http_ok("ok", $result->fetch_assoc()));
    } else {
        die(http_not_found());
    }

} elseif (isset($data->uid)) {
    if (!is_numeric($data->uid))
        die(http_bad_request());

    // Negative numbers represent current user
    $uid = $data->uid >= 0 ? (int)$data->uid : $_SESSION['uid'];

    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM `blogs` WHERE `uid` = ? ORDER BY `id` DESC LIMIT ? OFFSET ?;");
    if ($stmt === false)
        die(http_server_error());
    $stmt->bind_param("iii", $uid, $limit, $page);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $r = array();
        foreach ($result as $row)
            array_push($r, $row);
        exit(http_ok("ok", $r));
    } else {
        die(http_not_found());
    }
} else {
    // homepage
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM `blogs` ORDER BY `id` DESC LIMIT ? OFFSET ?;");
    $stmt->bind_param("ii", $limit, $page);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $r = array();
        foreach ($result as $row)
            array_push($r, $row);
        exit(http_ok("ok", $r));
    } else {
        die(http_not_found());
    }
}

