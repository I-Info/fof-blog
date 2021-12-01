<?php

require_once "config.php";

function statusBadRequest(): bool|string
{
    return json_encode(array(
        "code" => 400,
        "msg" => "bad request"
    ));
}

function statusFound(string $msg): bool|string
{
    return json_encode(array(
        "code" => 302,
        "msg" => $msg
    ));
}

function statusOK(string $msg = "ok"): bool|string
{
    return json_encode(array(
        "code" => 200,
        "msg" => $msg,
    ));
}

function statusForbidden(string $msg = "fail"): bool|string
{
    return json_encode(array(
        "code" => 403,
        "msg" => $msg,
    ));
}

function statusNotAllowed(string $msg = "not allowed"): bool|string
{
    return json_encode(array(
        "code" => 405,
        "msg" => $msg,
    ));
}

function debug()
{
    global $DEBUG;
    if (!$DEBUG)
        error_reporting(0);
}