<?php
include_once('auth_check.php');

//Check if user is logged in:
if ($authenticator->getUserLoginStatus()) {
    echo "true";
} else {
    echo "false";
};