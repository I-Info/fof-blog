<?php
require_once "../functions.php";
require_once "../config.php";

debug();
global $ADMIN;

$json_data = file_get_contents("php://input");
$data = json_decode($json_data);

if (!(isset($data->user) && isset($data->passwd))) {
    die(statusBadRequest());
}

session_start();

if (isset($_SESSION['uid'])) {
    if ($_SESSION['uid'] == $ADMIN['uid']) {
        exit(statusFound("admin"));
    } else {
        exit(statusFound("user"));
    }
}


if ($data->user == $ADMIN['uid'] && $data->passwd == $ADMIN['passwd']) {
    $_SESSION['uid'] = "admin";
    exit(statusOK());
} else
    exit(statusForbidden());
