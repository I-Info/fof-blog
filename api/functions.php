<?php
require_once "config.php";

function status_bad_request(string $msg = "bad request"): bool|string
{
    return json_encode(array(
        "code" => 400,
        "msg" => $msg,
    ));
}

function status_found(string $msg): bool|string
{
    return json_encode(array(
        "code" => 302,
        "msg" => $msg,
    ));
}

function status_ok(string $msg = "ok"): bool|string
{
    return json_encode(array(
        "code" => 200,
        "msg" => $msg,
    ));
}

function status_forbidden(string $msg = "fail"): bool|string
{
    return json_encode(array(
        "code" => 403,
        "msg" => $msg,
    ));
}

function status_not_allowed(string $msg = "not allowed"): bool|string
{
    return json_encode(array(
        "code" => 405,
        "msg" => $msg,
    ));
}

function status_server_error(string $msg = "internal server error"): bool|string
{
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

function mysql_connect(): mysqli
{
    global $DATABASE;
    try {
        $conn = mysqli_connect($DATABASE['hostname'], $DATABASE['username'], $DATABASE['password'],
            $DATABASE['database'], $DATABASE['port']);
        if ($conn->error) {
            die(status_server_error("database error"));
        }
    } catch (Exception $exception) {
        global $DEBUG;
        die(status_server_error($DEBUG ? $exception->getMessage() : "database error"));
    }
    return $conn;
}

function parse_json()
{
    return json_decode(file_get_contents("php://input"));
}