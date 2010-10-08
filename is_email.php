<?php
/**
 * To validate an email address according to RFCs 5321, 5322 and others
 * 
 * Copyright © 2008-2010, Dominic Sayers							<br>
 * Test schema documentation Copyright © 2010, Daniel Marschall				<br>
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     - Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     - Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     - Neither the name of Dominic Sayers nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package	is_email
 * @author	Dominic Sayers <dominic@sayers.cc>
 * @copyright	2008-2010 Dominic Sayers
 * @license	http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link	http://www.dominicsayers.com/isemail
 * @version	2.10.1 - Amended DNS lookup logic. Also, in is_email_statustext.php, changed $type to integer to allow for additional types (starting with SMTP codes)
 */

// The quality of this code has been improved greatly by using PHPLint
// Copyright (c) 2010 Umberto Salsi
// This is free software; see the license for copying conditions.
// More info: http://www.icosaedro.it/phplint/
/*.
	require_module 'standard';
	require_module 'pcre';
.*/
/**
 * Check that an email address conforms to RFCs 5321, 5322 and others
 *
 * @param string	$email		The email address to check
 * @param boolean	$checkDNS	If true then a DNS check for A and MX records will be made
 * @param mixed		$errorlevel	If true then return an integer error or warning number rather than true or false
 */
/*.mixed.*/ function is_email ($email, $checkDNS = false, $errorlevel = false) {
	// Check that $email is a valid address. Read the following RFCs to understand the constraints:
	// 	(http://tools.ietf.org/html/rfc5321)
	// 	(http://tools.ietf.org/html/rfc5322)
	// 	(http://tools.ietf.org/html/rfc4291#section-2.2)
	// 	(http://tools.ietf.org/html/rfc1123#section-2.1)
	// 	(http://tools.ietf.org/html/rfc3696) (guidance only)

	//	$errorlevel	Behaviour
	//	---------------	---------------------------------------------------------------------------
	//	E_ERROR		Return validation failures only. For technically valid addresses return
	//			ISEMAIL_VALID
	//	E_WARNING	Return warnings for unlikely but technically valid addresses. This includes
	//			addresses at TLDs (e.g. johndoe@com), addresses with FWS and comments,
	//			addresses that are quoted and addresses that contain no alphabetic or
	//			numeric characters.
	//	true		Same as E_ERROR
	//	false		Return true for valid addresses, false for invalid ones. No warnings.
	//
	//	Errors can be distinguished from warnings if ($return_value > ISEMAIL_ERROR)
// version 2.0: Enhance $diagnose parameter to $errorlevel
// revision 2.5: some syntax changes to make it more PHPLint-friendly. Should be functionally identical.

	if (!defined('ISEMAIL_VALID')) {
		// No errors
		define('ISEMAIL_VALID'			, 0);
		// Warnings (valid address but unlikely in the real world)
		define('ISEMAIL_WARNING'		, 64);
		define('ISEMAIL_TLD'			, 65);
		define('ISEMAIL_TLDNUMERIC'		, 66);
		define('ISEMAIL_QUOTEDSTRING'		, 67);
		define('ISEMAIL_COMMENTS'		, 68);
		define('ISEMAIL_FWS'			, 69);
		define('ISEMAIL_ADDRESSLITERAL'		, 70);
		define('ISEMAIL_UNLIKELYINITIAL'	, 71);
		define('ISEMAIL_SINGLEGROUPELISION'	, 72);
		define('ISEMAIL_DOMAINNOTFOUND'		, 73);
		define('ISEMAIL_MXNOTFOUND'		, 74);
		// Errors (invalid address)
		define('ISEMAIL_ERROR'			, 128);
		define('ISEMAIL_TOOLONG'		, 129);
		define('ISEMAIL_NOAT'			, 130);
		define('ISEMAIL_NOLOCALPART'		, 131);
		define('ISEMAIL_NODOMAIN'		, 132);
		define('ISEMAIL_ZEROLENGTHELEMENT'	, 133);
		define('ISEMAIL_BADCOMMENT_START'	, 134);
		define('ISEMAIL_BADCOMMENT_END'		, 135);
		define('ISEMAIL_UNESCAPEDDELIM'		, 136);
		define('ISEMAIL_EMPTYELEMENT'		, 137);
		define('ISEMAIL_UNESCAPEDSPECIAL'	, 138);
		define('ISEMAIL_LOCALTOOLONG'		, 139);
//		define('ISEMAIL_IPV4BADPREFIX'		, 140);
		define('ISEMAIL_IPV6BADPREFIXMIXED'	, 141);
		define('ISEMAIL_IPV6BADPREFIX'		, 142);
		define('ISEMAIL_IPV6GROUPCOUNT'		, 143);
		define('ISEMAIL_IPV6DOUBLEDOUBLECOLON'	, 144);
		define('ISEMAIL_IPV6BADCHAR'		, 145);
		define('ISEMAIL_IPV6TOOMANYGROUPS'	, 146);
		define('ISEMAIL_DOMAINEMPTYELEMENT'	, 147);
		define('ISEMAIL_DOMAINELEMENTTOOLONG'	, 148);
		define('ISEMAIL_DOMAINBADCHAR'		, 149);
		define('ISEMAIL_DOMAINTOOLONG'		, 150);
		define('ISEMAIL_IPV6SINGLECOLONSTART'	, 151);
		define('ISEMAIL_IPV6SINGLECOLONEND'	, 152);
		// Unexpected errors
//		define('ISEMAIL_BADPARAMETER'		, 190);
//		define('ISEMAIL_NOTDEFINED'		, 191);
// revision 2.1: Redefined unexpected error constants so they don't clash with the ISEMAIL_WARNING bit
// revision 2.5: Undefined unused constants
	}

	if (is_bool($errorlevel)) {
		if ((bool) $errorlevel) {
			$diagnose	= true;
			$warn		= false;
		} else {
			$diagnose	= false;
			$warn		= false;
		}
	} else {
		switch ((int) $errorlevel) {
		case E_WARNING:
			$diagnose	= true;
			$warn		= true;
			break;
		case E_ERROR:
			$diagnose	= true;
			$warn		= false;
			break;
		default:
			$diagnose	= false;
			$warn		= false;
		}
	}

	if ($diagnose) /*.mixed.*/ $return_status = ISEMAIL_VALID; else $return_status = true;
// version 2.0: Enhance $diagnose parameter to $errorlevel

	// the upper limit on address lengths should normally be considered to be 254
	// 	(http://www.rfc-editor.org/errata_search.php?rfc=3696)
	// 	NB My erratum has now been verified by the IETF so the correct answer is 254
	//
	// The maximum total length of a reverse-path or forward-path is 256
	// characters (including the punctuation and element separators)
	// 	(http://tools.ietf.org/html/rfc5321#section-4.5.3.1.3)
	//	NB There is a mandatory 2-character wrapper round the actual address
	$emailLength = strlen($email);
// revision 1.17: Max length reduced to 254 (see above)
	if ($emailLength > 254)			if ($diagnose) return ISEMAIL_TOOLONG;		else return false;	// Too long

	// Contemporary email addresses consist of a "local part" separated from
	// a "domain part" (a fully-qualified domain name) by an at-sign ("@").
	// 	(http://tools.ietf.org/html/rfc3696#section-3)
	$atIndex = strrpos($email,'@');

	if ($atIndex === false)			if ($diagnose) return ISEMAIL_NOAT;		else return false;	// No at-sign
	if ($atIndex === 0)			if ($diagnose) return ISEMAIL_NOLOCALPART;	else return false;	// No local part
	if ($atIndex === $emailLength - 1)	if ($diagnose) return ISEMAIL_NODOMAIN;		else return false;	// No domain part
// revision 1.14: Length test bug suggested by Andrew Campbell of Gloucester, MA

	// Sanitize comments
	// - remove nested comments, quotes and dots in comments
	// - remove parentheses and dots from quoted strings
	$braceDepth	= 0;
	$inQuote	= false;
	$escapeThisChar	= false;

	for ($i = 0; $i < $emailLength; ++$i) {
		$char = $email[$i];
		$replaceChar = false;

		if ($char === '\\') 	$escapeThisChar = !$escapeThisChar;			// Escape the next character?
		else {
			switch ($char) {
			case '(':
				if	($escapeThisChar)	$replaceChar	= true;
				else if	($inQuote)		$replaceChar	= true;
				else if	($braceDepth++ > 0)	$replaceChar	= true;		// Increment brace depth

				break;
			case ')':
				if	($escapeThisChar)	$replaceChar	= true;
				else if	($inQuote)		$replaceChar	= true;
				else {
					if (--$braceDepth > 0)	$replaceChar	= true;		// Decrement brace depth
					if ($braceDepth < 0)	$braceDepth	= 0;
				}

				break;
			case '"':
				if	($escapeThisChar)	$replaceChar	= true;
				else if ($braceDepth === 0)	$inQuote	= !$inQuote;	// Are we inside a quoted string?
				else				$replaceChar	= true;

				break;
			case '.':
				if	($escapeThisChar)	$replaceChar	= true;		// Dots don't help us either
				else if	($braceDepth > 0)	$replaceChar	= true;

				break;
			default:
			}

			$escapeThisChar = false;
//			if ($replaceChar) $email[$i] = 'x';					// Replace the offending character with something harmless
// revision 1.12: Line above replaced because PHPLint doesn't like that syntax
			if ($replaceChar) $email = (string) substr_replace($email, 'x', $i, 1);	// Replace the offending character with something harmless
		}
	}

	$localPart	= substr($email, 0, $atIndex);
	$domain		= substr($email, $atIndex + 1);
	$FWS		= "(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t]+)|(?:[ \\t]+(?:(?:\\r\\n)[ \\t]+)*))";	// Folding white space
	$dotArray	= /*. (array[]) .*/ array();

	// Let's check the local part for RFC compliance...
	//
	// local-part      =       dot-atom / quoted-string / obs-local-part
	// obs-local-part  =       word *("." word)
	// 	(http://tools.ietf.org/html/rfc5322#section-3.4.1)
	//
	// Problem: need to distinguish between "first.last" and "first"."last"
	// (i.e. one element or two). And I suck at regular expressions.
	$dotArray	= preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $localPart);
	$partLength	= 0;

	foreach ($dotArray as $arrayMember) {
		$element = (string) $arrayMember;
		// Remove any leading or trailing FWS
		$new_element = preg_replace("/^$FWS|$FWS\$/", '', $element);
		if ($warn && ($element !== $new_element)) $return_status = ISEMAIL_FWS;	// FWS is unlikely in the real world
		$element = $new_element;
// version 2.3: Warning condition added
		$elementLength	= strlen($element);

		if ($elementLength === 0)								if ($diagnose) return ISEMAIL_ZEROLENGTHELEMENT;	else return false;	// Can't have empty element (consecutive dots or dots at the start or end)
// revision 1.15: Speed up the test and get rid of "uninitialized string offset" notices from PHP

		// We need to remove any valid comments (i.e. those at the start or end of the element)
		if ($element[0] === '(') {
			if ($warn) $return_status = ISEMAIL_COMMENTS;	// Comments are unlikely in the real world
// version 2.0: Warning condition added
			$indexBrace = strpos($element, ')');
			if ($indexBrace !== false) {
				if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0)
													if ($diagnose) return ISEMAIL_BADCOMMENT_START;		else return false;	// Illegal characters in comment
				$element	= substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
				$elementLength	= strlen($element);
			}
		}

		if ($element[$elementLength - 1] === ')') {
			if ($warn) $return_status = ISEMAIL_COMMENTS;	// Comments are unlikely in the real world
// version 2.0: Warning condition added
			$indexBrace = strrpos($element, '(');
			if ($indexBrace !== false) {
				if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0)
													if ($diagnose) return ISEMAIL_BADCOMMENT_END;		else return false;	// Illegal characters in comment
				$element	= substr($element, 0, $indexBrace);
				$elementLength	= strlen($element);
			}
		}

		// Remove any remaining leading or trailing FWS around the element (having removed any comments)
		$new_element = preg_replace("/^$FWS|$FWS\$/", '', $element);
		if ($warn && ($element !== $new_element)) $return_status = ISEMAIL_FWS;	// FWS is unlikely in the real world
		$element = $new_element;
// version 2.0: Warning condition added

		// What's left counts towards the maximum length for this part
		if ($partLength > 0) $partLength++;	// for the dot
		$partLength += strlen($element);

		// Each dot-delimited component can be an atom or a quoted string
		// (because of the obs-local-part provision)
		if (preg_match('/^"(?:.)*"$/s', $element) > 0) {
			// Quoted-string tests:
			if ($warn) $return_status = ISEMAIL_QUOTEDSTRING;	// Quoted string is unlikely in the real world
// version 2.0: Warning condition added
			// Remove any FWS
			$element = preg_replace("/(?<!\\\\)$FWS/", '', $element);	// A warning condition, but we've already raised ISEMAIL_QUOTEDSTRING
			// My regular expression skills aren't up to distinguishing between \" \\" \\\" \\\\" etc.
			// So remove all \\ from the string first...
			$element = preg_replace('/\\\\\\\\/', ' ', $element);
			if (preg_match('/(?<!\\\\|^)["\\r\\n\\x00](?!$)|\\\\"$|""/', $element) > 0)	if ($diagnose) return ISEMAIL_UNESCAPEDDELIM;		else return false;	// ", CR, LF and NUL must be escaped
// version 2.0: allow ""@example.com because it's technically valid
		} else {
			// Unquoted string tests:
			//
			// Period (".") may...appear, but may not be used to start or end the
			// local part, nor may two or more consecutive periods appear.
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// A zero-length element implies a period at the beginning or end of the
			// local part, or two periods together. Either way it's not allowed.
			if ($element === '')								if ($diagnose) return ISEMAIL_EMPTYELEMENT;		else return false;	// Dots in wrong place

			// Any ASCII graphic (printing) character other than the
			// at-sign ("@"), backslash, double quote, comma, or square brackets may
			// appear without quoting.  If any of that list of excluded characters
			// are to appear, they must be quoted
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// Any excluded characters? i.e. 0x00-0x20, (, ), <, >, [, ], :, ;, @, \, comma, period, "
			if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]/', $element) > 0)	if ($diagnose) return ISEMAIL_UNESCAPEDSPECIAL;		else return false;	// These characters must be in a quoted string
			if ($warn && (preg_match('/^\\w+/', $element) === 0)) $return_status = ISEMAIL_UNLIKELYINITIAL;	// First character is an odd one
		}
	}

	if ($partLength > 64)										if ($diagnose) return ISEMAIL_LOCALTOOLONG;		else return false;	// Local part must be 64 characters or less

	// Now let's check the domain part...

	// The domain name can also be replaced by an IP address in square brackets
	// 	(http://tools.ietf.org/html/rfc3696#section-3)
	// 	(http://tools.ietf.org/html/rfc5321#section-4.1.3)
	// 	(http://tools.ietf.org/html/rfc4291#section-2.2)
	if (preg_match('/^\\[(.)+]$/', $domain) === 1) {
		// It's an address-literal
		if ($warn) $return_status = ISEMAIL_ADDRESSLITERAL;	// Quoted string is unlikely in the real world
// version 2.0: Warning condition added
		$addressLiteral = substr($domain, 1, strlen($domain) - 2);
		$groupMax	= 8;
// revision 2.1: new IPv6 testing strategy
		$matchesIP	= array();
		$colon		= ':';	// Revision 2.7: Daniel Marschall's new IPv6 testing strategy
		$double_colon	= '::';

		// Extract IPv4 part from the end of the address-literal (if there is one)
		if (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
			$index = strrpos($addressLiteral, $matchesIP[0]);

			if ($index === 0) {
				// Nothing there except a valid IPv4 address, so...
				if ($diagnose) return $return_status; else return true;
// version 2.0: return warning if one is set
			} else {
//-				// Assume it's an attempt at a mixed address (IPv6 + IPv4)
//-				if ($addressLiteral[$index - 1] !== $colon)				if ($diagnose) return ISEMAIL_IPV4BADPREFIX;		else return false;	// Character preceding IPv4 address must be ':'
// revision 2.1: new IPv6 testing strategy
				if (substr($addressLiteral, 0, 5) !== 'IPv6:')				if ($diagnose) return ISEMAIL_IPV6BADPREFIXMIXED;	else return false;	// RFC5321 section 4.1.3
//-
//-				$IPv6		= substr($addressLiteral, 5, ($index === 7) ? 2 : $index - 6);
//-				$groupMax	= 6;
// revision 2.1: new IPv6 testing strategy
				$IPv6		= substr($addressLiteral, 5, $index - 5) . '0000:0000'; // Convert IPv4 part to IPv6 format
			}
		} else {
			// It must be an attempt at pure IPv6
			if (substr($addressLiteral, 0, 5) !== 'IPv6:')					if ($diagnose) return ISEMAIL_IPV6BADPREFIX;		else return false;	// RFC5321 section 4.1.3
			$IPv6 = substr($addressLiteral, 5);
//-			$groupMax = 8;
// revision 2.1: new IPv6 testing strategy
		}

		$matchesIP	= explode($colon, $IPv6);	// Revision 2.7: Daniel Marschall's new IPv6 testing strategy
		$groupCount	= count($matchesIP);
		$index		= strpos($IPv6,$double_colon);

		if ($index === false) {
			// We need exactly the right number of groups
			if ($groupCount !== $groupMax)							if ($diagnose) return ISEMAIL_IPV6GROUPCOUNT;		else return false;	// RFC5321 section 4.1.3
		} else {
			if ($index !== strrpos($IPv6,$double_colon))					if ($diagnose) return ISEMAIL_IPV6DOUBLEDOUBLECOLON;	else return false;	// More than one '::'
			if ($index === 0 || $index === (strlen($IPv6) - 2)) $groupMax++;	// RFC 4291 allows :: at the start or end of an address with 7 other groups in addition
			if ($groupCount > $groupMax)							if ($diagnose) return ISEMAIL_IPV6TOOMANYGROUPS;	else return false;	// Too many IPv6 groups in address
			if ($groupCount === $groupMax) $return_status = ISEMAIL_SINGLEGROUPELISION;	// Eliding a single group with :: is deprecated by RFCs 5321 & 5952
		}

		// Check for single : at start and end of address
		// Revision 2.7: Daniel Marschall's new IPv6 testing strategy
		if ((substr($IPv6, 0,  1)	=== $colon) && (substr($IPv6, 1,  1) !== $colon))	if ($diagnose) return ISEMAIL_IPV6SINGLECOLONSTART;	else return false;	// Address starts with a single colon
		if ((substr($IPv6, -1)		=== $colon) && (substr($IPv6, -2, 1) !== $colon))	if ($diagnose) return ISEMAIL_IPV6SINGLECOLONEND;	else return false;	// Address ends with a single colon

		// Check for unmatched characters
		if (count(preg_grep('/^[0-9A-Fa-f]{0,4}$/', $matchesIP, PREG_GREP_INVERT)) !== 0)	if ($diagnose) return ISEMAIL_IPV6BADCHAR;		else return false;	// Illegal characters in address
		// It's a valid IPv6 address, so...
		if ($diagnose) return $return_status; else return true;
// revision 2.1: bug fix: now correctly return warning status
	} else {
		// It's a domain name...

		// The syntax of a legal Internet host name was specified in RFC-952
		// One aspect of host name syntax is hereby changed: the
		// restriction on the first character is relaxed to allow either a
		// letter or a digit.
		// 	(http://tools.ietf.org/html/rfc1123#section-2.1)
		//
		// NB RFC 1123 updates RFC 1035, but this is not currently apparent from reading RFC 1035.
		//
		// Most common applications, including email and the Web, will generally not
		// permit...escaped strings
		// 	(http://tools.ietf.org/html/rfc3696#section-2)
		//
		// the better strategy has now become to make the "at least one period" test,
		// to verify LDH conformance (including verification that the apparent TLD name
		// is not all-numeric)
		// 	(http://tools.ietf.org/html/rfc3696#section-2)
		//
		// Characters outside the set of alphabetic characters, digits, and hyphen MUST NOT appear in domain name
		// labels for SMTP clients or servers
		// 	(http://tools.ietf.org/html/rfc5321#section-4.1.2)
		//
		// RFC5321 precludes the use of a trailing dot in a domain name for SMTP purposes
		// 	(http://tools.ietf.org/html/rfc5321#section-4.1.2)
		$dotArray	= preg_split('/\\.(?=(?:[^\\"]*\\"[^\\"]*\\")*(?![^\\"]*\\"))/m', $domain);
		$partLength	= 0;
		$element	= ''; // Since we use $element after the foreach loop let's make sure it has a value
// revision 1.13: Line above added because PHPLint now checks for Definitely Assigned Variables

		if ($warn && (count($dotArray) === 1))	$return_status = ISEMAIL_TLD;	// The mail host probably isn't a TLD
// version 2.0: downgraded to a warning

		foreach ($dotArray as $arrayMember) {
			$element = (string) $arrayMember;
			// Remove any leading or trailing FWS
			$new_element	= preg_replace("/^$FWS|$FWS\$/", '', $element);
			if ($warn && ($element !== $new_element)) $return_status = ISEMAIL_FWS;	// FWS is unlikely in the real world
			$element = $new_element;
// version 2.0: Warning condition added
			$elementLength	= strlen($element);

			// Each dot-delimited component must be of type atext
			// A zero-length element implies a period at the beginning or end of the
			// local part, or two periods together. Either way it's not allowed.
			if ($elementLength === 0)							if ($diagnose) return ISEMAIL_DOMAINEMPTYELEMENT;	else return false;	// Dots in wrong place
// revision 1.15: Speed up the test and get rid of "uninitialized string offset" notices from PHP

			// Then we need to remove all valid comments (i.e. those at the start or end of the element
			if ($element[0] === '(') {
				if ($warn) $return_status = ISEMAIL_COMMENTS;	// Comments are unlikely in the real world
// version 2.0: Warning condition added
				$indexBrace = strpos($element, ')');
				if ($indexBrace !== false) {
					if (preg_match('/(?<!\\\\)[\\(\\)]/', substr($element, 1, $indexBrace - 1)) > 0)
													if ($diagnose) return ISEMAIL_BADCOMMENT_START;		else return false;	// Illegal characters in comment
// revision 1.17: Fixed name of constant (also spotted by turboflash - thanks!)
					$element	= substr($element, $indexBrace + 1, $elementLength - $indexBrace - 1);
					$elementLength	= strlen($element);
				}
			}

			if ($element[$elementLength - 1] === ')') {
				if ($warn) $return_status = ISEMAIL_COMMENTS;	// Comments are unlikely in the real world
// version 2.0: Warning condition added
				$indexBrace = strrpos($element, '(');
				if ($indexBrace !== false) {
					if (preg_match('/(?<!\\\\)(?:[\\(\\)])/', substr($element, $indexBrace + 1, $elementLength - $indexBrace - 2)) > 0)
													if ($diagnose) return ISEMAIL_BADCOMMENT_END;		else return false;	// Illegal characters in comment
// revision 1.17: Fixed name of constant (also spotted by turboflash - thanks!)
					$element	= substr($element, 0, $indexBrace);
					$elementLength	= strlen($element);
				}
			}

			// Remove any leading or trailing FWS around the element (inside any comments)
			$new_element	= preg_replace("/^$FWS|$FWS\$/", '', $element);
			if ($warn && ($element !== $new_element)) $return_status = ISEMAIL_FWS;	// FWS is unlikely in the real world
			$element = $new_element;
// version 2.0: Warning condition added

			// What's left counts towards the maximum length for this part
			if ($partLength > 0) $partLength++;	// for the dot
			$partLength += strlen($element);

			// The DNS defines domain name syntax very generally -- a
			// string of labels each containing up to 63 8-bit octets,
			// separated by dots, and with a maximum total of 255
			// octets.
			// 	(http://tools.ietf.org/html/rfc1123#section-6.1.3.5)
			if ($elementLength > 63)							if ($diagnose) return ISEMAIL_DOMAINELEMENTTOOLONG;	else return false;	// Label must be 63 characters or less

			// Any ASCII graphic (printing) character other than the
			// at-sign ("@"), backslash, double quote, comma, or square brackets may
			// appear without quoting.  If any of that list of excluded characters
			// are to appear, they must be quoted
			// 	(http://tools.ietf.org/html/rfc3696#section-3)
			//
			// If the hyphen is used, it is not permitted to appear at
			// either the beginning or end of a label.
			// 	(http://tools.ietf.org/html/rfc3696#section-2)
			//
			// Any excluded characters? i.e. 0x00-0x20, (, ), <, >, [, ], :, ;, @, \, comma, period, "
			if (preg_match('/[\\x00-\\x20\\(\\)<>\\[\\]:;@\\\\,\\."]|^-|-$/', $element) > 0) if ($diagnose) return ISEMAIL_DOMAINBADCHAR;		else return false;	// Illegal character in domain name
		}

		if ($partLength > 255) 									if ($diagnose) return ISEMAIL_DOMAINTOOLONG;		else return false;	// Domain part must be 255 characters or less (http://tools.ietf.org/html/rfc1123#section-6.1.3.5)

		if ($warn && (preg_match('/^[0-9]+$/', $element) > 0))	$return_status = ISEMAIL_TLDNUMERIC;	// TLD probably isn't all-numeric (http://www.apps.ietf.org/rfc/rfc3696.html#sec-2)
// version 2.0: Downgraded to a warning

		// Check DNS?
		if ($diagnose && ($return_status === ISEMAIL_VALID) && $checkDNS && function_exists('checkdnsrr')) {
			// Revision 2.10: Amended DNS logic
			// An A-record is not required unless there are no MX-records
			// for a domain. Obvious when you think about it.
			// 	(http://tools.ietf.org/html/rfc5321#section-5)
			// 	(http://tools.ietf.org/html/rfc2181#section-10.3)
			// 	(http://tools.ietf.org/html/rfc1035)
			if (!(checkdnsrr($domain, 'MX'))) {
				$result = @dns_get_record($domain, DNS_A);

				if ((is_bool($result) && !(bool) $result))
					$return_status = ISEMAIL_DOMAINNOTFOUND;	// Neither MX- nor A-record for domain can be found
				else	$return_status = ISEMAIL_MXNOTFOUND;		// MX-record for domain can't be found
			}
		}
	}

	// Eliminate all other factors, and the one which remains must be the truth.
	// 	(Sherlock Holmes, The Sign of Four)
	if ($diagnose) return $return_status; else return true;
// version 2.0: return warning if one is set
}
?>
