is_email()
==========

Copyright 2008-2016 Dominic Sayers <dominic@sayers.cc>

https://isemail.info

BSD License (https://www.opensource.org/licenses/bsd-license.php)

How to use is_email()
---------------------
1.  Add the downloaded file is_email.php to your project
1.  In your scripts use it like this:

```php
	require_once 'is_email.php';
	if (is_email($email)) echo "$email is a valid email address";
```

1.  If you want to return detailed diagnostic error codes then you can ask
is_email to do so. Something like this should work:

```php
	require_once 'is_email.php';
	$email = 'dominic@sayers.cc';
	$result = is_email($email, true, true);

	if ($result === ISEMAIL_VALID) {
		echo "$email is a valid email address";
	} else if ($result < ISEMAIL_THRESHOLD) {
		echo "Warning! $email has unusual features (result code $result)";
	} else {
		echo "$email is not a valid email address (result code $result)";
	}
```

1.  Example scripts are in the extras folder

Version history
---------------

| Date | Component | Version | Notes |
| ---- | --------- | ------- | ----- |
| 2016-12-05 | <all> | 3.06 | Changed all http links to https. Updated my links. Updated copyright. Clarified license |
| 2013-11-29 | tests.xml | 3.05 | Changed Test #71 from ISEMAIL_RFC5321 to ISEMAIL_DEPREC |
| 2013-11-29 | meta.xml | 3.05 | Changed category of ISEMAIL_RFC5321_IPV6DEPRECATED to ISEMAIL_DEPREC |
| 2011-07-14 | tests.xml | 3.04 | Changed my link to https://isemail.info |
| 2011-05-23 | tests.xml | 3.02 | tests.php:  Argument no longer passed by reference (deprecated). Test#32: Changed domain to c--n.com because g--a.com no longer has an MX record. |
| 2010-11-15 | meta.xml | 3.03 | Clarified definition of Valid for numpties |
| 2010-10-18 | tests.xml | 3.0 |New schema designed to enhance fault identification. |
| 2010-10-18 | is_email.php | 3.0 | Forensic categorization of email validity |
