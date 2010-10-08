is_email()
Copyright 2008-2010 Dominic Sayers <dominic@sayers.cc>
http://www.dominicsayers.com/isemail
BSD License (http://www.opensource.org/licenses/bsd-license.php)

--------------------------------------------------------------------------------
How to use is_email()
--------------------------------------------------------------------------------
1. Add the downloaded file is_email.php to your project
2. In your scripts use it like this:

	require_once 'is_email.php';
	if (is_email($email)) echo "$email is a valid email address";

3. If you want to return detailed diagnostic error codes then you can ask
is_email to do so. Something like this should work:

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

4. Example scripts are in the extras folder

--------------------------------------------------------------------------------
Version history
--------------------------------------------------------------------------------

// Revision 2.10: Amended DNS lookup logic. Also, in is_email_statustext.php, changed $type to integer to allow for additional types (starting with SMTP codes)

Test suite version 2.6
	2010-10-08	After researching the logic of SMTP routing, I now
			believe the lack of an A-record for us.ibm.com is
			absolutely fine and should not raise a warning. If
			there is an MX-record for a domain then that's all we
			need. If there is no MX-record then an A-record can
			be used as a fallback for historical reasons.

			I've changed test #63 so that it doesn't expect a
			warning and added #277 to test the existence of
			an A-record without an associated MX-record.

// Revision 2.9: No functional change to is_email.php, but language correctly declared in tests.xsd, DOCTYPE declared in tests.xml, BOM removed from readme.txt

// Revision 2.8: is_email_statustext.php text amended to more accurately reflect the error condition of ISEMAIL_IPV6BADCHAR

Test suite version 2.5
	2010-10-04	My mum's birthday. Happy birthday, mum. Added test #276
			to test missing outcome of ISEMAIL_IPV6TOOMANYGROUPS. Thanks
			to Daniel Marschall for suggesting this.


// Revision 2.7: Daniel Marschall's new IPv6 testing strategy

Test suite version 2.4
	2010-10-01	In test #63 the status of the domain us.ibm.com is
			somewhat unclear. It doesn't appear to have an A
			record of its own so we are expecting a warning.
			In my own testing, an A record was found but this
			turned out to be an artifact of using the OpenDNS
			free service: OpenDNS was kindly giving me a page of
			ads as an erroneous positive result.

// Revision 2.6: BUG: The online test page didn't take account of the magic_quotes_gpc setting that some hosting providers insist on setting. Including mine.

// Revision 2.5: Some syntax changes to make it more PHPLint-friendly. Should be functionally identical.

// Revision 2.4: Workaround for PHP bug (http://bugs.php.net/48645) in test script

// revision 2.3: Fixed FWS bug suggested by John Kloor. Test #152 result corrected

Test suite version 2.3
	2010-09-13	John Kloor kindly pointed out that folding white space
			in the local part was sometimes not raising a warning.
			This should have been picked up by test #152 but the
			test was incorrectly marked as not expecting a
			warning. This is now fixed. I have also amended test
			#223 (which was a duplicate) so that it reflects
			John's suggested test. My thanks to him for his
			contribution.

// revision 2.2: Much tidying and debugging of tests led by Daniel Marschall

Test suite version 2.2
	2010-09-10	26 tests were marked as being invalid addresses but were
			still marked as expecting a warning. Cannot be both
			invalid and a warning. Thanks to Daniel Marschall for
			finding this.

			I've changed all the example.com tests to iana.org so
			that we are testing for a domain that does have an MX
			record. I've also change the .museum and .edu tests
			in a similar way. Where the test is still for a non-
			existent domain I've changed the expectation to Warning

// revision 2.1: Revisited IPv6 address validation in the light of RFC 4291
// version 2.0: Strict tests for validity, optional warnings for unlikely real-world addresses
// revision 1.18: Standardised build and release process for this script
// revision 1.17: Upper length limit corrected to 254 characters
// revision 1.16: Added optional diagnosis codes (amended all lines with a return statement)
// revision 1.15: Speed up the test and get rid of "unitialized string offset" notices from PHP
// revision 1.14: Length test bug suggested by Andrew Campbell of Gloucester, MA
// revision 1.13: Line added because PHPLint now checks for Definitely Assigned Variables
// revision 1.12: Line replaced because PHPLint doesn't like that syntax
