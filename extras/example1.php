<?php
require_once 'is_email.php';
$email = 'dominic@sayers.cc';
if (is_email($email)) echo "$email is a valid email address";
?>