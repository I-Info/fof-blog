<?php
require_once "../functions.php";
debug();
session_start();
if (isset($_SESSION['uid'])) {
    session_destroy();
    print http_ok();
} else {
    print http_not_allowed();
}
