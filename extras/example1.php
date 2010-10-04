<?php
require_once 'is_email.php';
$address = 'dominic@sayers.cc';
if (is_email($address)) echo "$address is a valid email address";
?>