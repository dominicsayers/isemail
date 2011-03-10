<pre>
<?php
require_once 'is_email.php';
require_once 'is_email_statustext.php';

$email = 'dominic@sayers.cc';

$result = is_email($email, true, E_WARNING);

echo "Result is $result" . PHP_EOL;
echo 'Result description is ' . 	is_email_statustext($result, ISEMAIL_STATUSTEXT_EXPLANATORY) . PHP_EOL;
echo 'PHP constant name is ' .		is_email_statustext($result, ISEMAIL_STATUSTEXT_CONSTANT) . PHP_EOL;
echo 'SMTP enhanced status code is ' .	is_email_statustext($result, ISEMAIL_STATUSTEXT_SMTPCODE) . PHP_EOL;
?>
</pre>