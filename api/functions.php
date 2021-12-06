<?php
require_once "config.php";

function http_bad_request(string $msg = "bad request"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 400,
        "msg" => $msg,
    ));
}

function http_not_found(string $msg = "not found"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 404,
        "msg" => $msg,
    ));
}

function http_found(string $msg = "found"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 302,
        "msg" => $msg,
    ));
}

function http_unauthorized(string $msg = "unauthorized"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 401,
        "msg" => $msg,
    ));
}

function http_ok(string $msg = "ok", array $data = null): bool|string
{
    header('Content-type: application/json');
    if ($data) {
        return json_encode(array(
            "code" => 200,
            "msg" => $msg,
            "data" => $data,
        ));
    } else {
        return json_encode(array(
            "code" => 200,
            "msg" => $msg,
        ));
    }
}

function http_forbidden(string $msg = "forbidden"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 403,
        "msg" => $msg,
    ));
}

function http_not_allowed(string $msg = "not allowed"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 405,
        "msg" => $msg,
    ));
}

function http_server_error(string $msg = "internal server error"): bool|string
{
    header('Content-type: application/json');
    return json_encode(array(
        "code" => 500,
        "msg" => $msg,
    ));
}

function debug()
{
    global $DEBUG;
    if (!$DEBUG)
        error_reporting(0);
}

/**
 * @return mysqli
 */
function db_connect(): mysqli
{
    global $DATABASE;
    try {
        $conn = mysqli_connect($DATABASE['hostname'], $DATABASE['username'], $DATABASE['password'],
            $DATABASE['database'], $DATABASE['port']);
        if ($conn->error) {
            die(http_server_error("database error"));
        }
    } catch (Exception $exception) {
        global $DEBUG;
        die(http_server_error($DEBUG ? $exception->getMessage() : "database error"));
    }
    return $conn;
}

function parse_json()
{
    return json_decode(file_get_contents("php://input"));
}

/**
 * return uid when already logged in
 * @return false|string
 */
function check_log_status(): false|string
{
    session_start();
    if (isset($_SESSION['uid'])) {
        return $_SESSION['uid'];
    }
    return false;
}

function query_prepared(mysqli $conn, $query, array $args): bool|mysqli_result|int|string
{
    error_reporting(0);
    $stmt = $conn->prepare($query);
    $params = [];
    $types = array_reduce($args, function ($string, &$arg) use (&$params) {
        $params[] = &$arg;
        if (is_float($arg)) $string .= 'd';
        elseif (is_integer($arg)) $string .= 'i';
        elseif (is_string($arg)) $string .= 's';
        else                        $string .= 'b';
        return $string;
    }, '');
    array_unshift($params, $types);

    call_user_func_array([$stmt, 'bind_param'], $params);

    $result = $stmt->execute() ? ($stmt->get_result() ? $stmt->get_result() : $stmt->affected_rows) : false;

    $stmt->close();
    global $DEBUG;
    error_reporting($DEBUG ? 4 : 0);
    return $result;
}

