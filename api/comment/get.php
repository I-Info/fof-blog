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


if (isset($data->id)) {
    // get comment by id
    if (!is_numeric($data->id))
        die(http_bad_request());
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT content, uid, blog_id, likes, create_time, update_time FROM `comments` WHERE id = ?;");
    if ($stmt === false)
        die(http_server_error());

    $stmt->bind_param("i", $id);
    $id = $data->id;
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows) {
            exit(http_ok("ok", $result->fetch_assoc()));
        }
        exit(http_not_found());
    } catch (mysqli_sql_exception $exception) {
        global $DEBUG;
        exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
    }
} elseif (isset($data->blog_id)) {
    // get comments by blog id
    if (!is_numeric($data->blog_id))
        die(http_bad_request());
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT id, content, uid, likes, create_time, update_time FROM `comments` WHERE blog_id = ? ORDER BY `id` DESC, likes DESC LIMIT ? OFFSET ?;");
    if ($stmt === false)
        die(http_server_error());

    $stmt->bind_param("iii", $id, $limit, $page);
    $id = $data->blog_id;
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows) {
            $r = array();
            foreach ($result as $row) {
                $r[] = $row;
            }
            exit(http_ok("ok", $r));
        }
        exit(http_not_found());
    } catch (mysqli_sql_exception $exception) {
        global $DEBUG;
        exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
    }

} else {
    // get current user's comments
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT id, blog_id, content, likes, create_time, update_time FROM `comments` WHERE uid = ? ORDER BY `id` DESC LIMIT ? OFFSET ?;");
    if ($stmt === false)
        die(http_server_error());

    $stmt->bind_param("iii", $uid, $limit, $page);
    try {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows) {
            $r = array();
            foreach ($result as $row) {
                $r[] = $row;
            }
            exit(http_ok("ok", $r));
        }
        exit(http_not_found());
    } catch (mysqli_sql_exception $exception) {
        global $DEBUG;
        exit(http_bad_request($DEBUG ? "c:" . $exception->getCode() . "m:" . $exception->getMessage() : "bad request"));
    }
}