<?php
require_once 'is_email.php';

$address = 'dominic@sayers.cc';

$result = is_email($address, true, E_WARNING);

if ($result === ISEMAIL_VALID) {
	echo "$address is a valid email address";
} else if ($result < ISEMAIL_ERROR) {
	echo "Warning! $address may not be a real email address (result code $result)";
} else {
	echo "$address is not a valid email address (result code $result)";
}
?>