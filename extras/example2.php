<?php
require_once 'is_email.php';

$email = 'dominic@sayers.cc';

$result = is_email($email, true, E_WARNING);

if ($result === ISEMAIL_VALID) {
	echo "$email is a valid email address";
} else if ($result < ISEMAIL_ERROR) {
	echo "Warning! $email may not be a real email address (result code $result)";
} else {
	echo "$email is not a valid email address (result code $result)";
}
?>