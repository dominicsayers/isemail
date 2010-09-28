is_email()
Copyright 2008-2010 Dominic Sayers <dominic@sayers.cc>
http://www.dominicsayers.com/isemail
BSD License (http://www.opensource.org/licenses/bsd-license.php)

// Revision 2.5: Some syntax changes to make it more PHPLint-friendly. Should be functionally identical.

// Revision 2.4: Workaround for PHP bug (http://bugs.php.net/48645) in test script

// revision 2.3: Fixed FWS bug suggested by John Kloor. Test #152 result corrected

2.3	2010-09-13	John Kloor kindly pointed out that folding white space
			in the local part was sometimes not raising a warning.
			This should have been picked up by test #152 but the
			test was incorrectly marked as not expecting a
			warning. This is now fixed. I have also amended test
			#223 (which was a duplicate) so that it reflects
			John's suggested test. My thanks to him for his
			contribution.

// revision 2.2: Much tidying and debugging of tests led by Daniel Marschall

2.2	2010-09-10	26 tests were marked as being invalid addresses but were
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
