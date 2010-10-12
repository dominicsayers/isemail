<?php
// Revision 2.10: Changed $type to integer to allow for additional types, starting with SMTP codes

// What type of status text to return
if (!defined('ISEMAIL_STATUSTEXT_EXPLANATORY')) {
	define('ISEMAIL_STATUSTEXT_EXPLANATORY'	, 1);	// Explanatory text for this $status
	define('ISEMAIL_STATUSTEXT_CONSTANT'	, 2);	// The name of the constant for this $status
	define('ISEMAIL_STATUSTEXT_SMTPCODE'	, 3);	// The SMTP enhanced status code for this $status (the bounce message)
	define('ISEMAIL_STATUSTEXT_INVALIDTYPE'	, -1);	// Unrecognised $type

	// SMTP enhanced status messages
	define('ISEMAIL_STATUSTEXT_SMTP_250_215'	, '250 2.1.5 ok');
	define('ISEMAIL_STATUSTEXT_SMTP_553_510'	, '553 5.1.0 Other address status');
	define('ISEMAIL_STATUSTEXT_SMTP_553_511'	, '553 5.1.1 Bad destination mailbox address');
	define('ISEMAIL_STATUSTEXT_SMTP_553_512'	, '553 5.1.2 Bad destination system address');
	define('ISEMAIL_STATUSTEXT_SMTP_553_513'	, '553 5.1.3 Bad destination mailbox address syntax');
}

/*
 * Return a text status message depending on the is_email() return status
 */
/*.string.*/ function is_email_statustext(/*.integer.*/ $status, /*.mixed.*/ $type = true) {
	// For backward compatibility we recognise a boolean $type
	if	(is_int($type))		$effective_type	= $type;
	else if	(is_bool($type))	$effective_type	= ((bool) $type) ? ISEMAIL_STATUSTEXT_EXPLANATORY : ISEMAIL_STATUSTEXT_CONSTANT;
	else				$effective_type	= ISEMAIL_STATUSTEXT_INVALIDTYPE;

	// Return status text depending on $effective_type and $status
	switch ($effective_type) {
	case ISEMAIL_STATUSTEXT_EXPLANATORY:
		switch ($status) {
		case ISEMAIL_VALID:			return 'Address is valid';											break;	// 0
		// Warnings (valid address but unlikely in the real world)
		case ISEMAIL_WARNING:			return 'Address is valid but unlikely in the real world';							break;	// 64
		case ISEMAIL_TLD:			return 'Address is valid but at a Top Level Domain';								break;	// 65
		case ISEMAIL_TLDNUMERIC:		return 'Address is valid but the Top Level Domain is numeric';							break;	// 66
		case ISEMAIL_QUOTEDSTRING:		return 'Address is valid but contains a quoted string';								break;	// 67
		case ISEMAIL_COMMENTS:			return 'Address is valid but contains comments';								break;	// 68
		case ISEMAIL_FWS:			return 'Address is valid but contains Floating White Space';							break;	// 69
		case ISEMAIL_ADDRESSLITERAL:		return 'Address is valid but at a literal address not a domain';						break;	// 70
		case ISEMAIL_UNLIKELYINITIAL:		return 'Address is valid but has an unusual initial letter';							break;	// 71
		case ISEMAIL_SINGLEGROUPELISION:	return 'Address is valid but contains a :: that only elides one zero group';					break;	// 72
		case ISEMAIL_DOMAINNOTFOUND:		return 'Couldn\'t find an MX-record or an A-record for this domain';						break;	// 73 Revision 2.10: text amended to reflect new DNS logic
		case ISEMAIL_MXNOTFOUND:		return 'Couldn\'t find an MX record for this domain but an A-record does exist';				break;	// 74 Revision 2.10: text amended to reflect new DNS logic
		// Errors (invalid address)
		case ISEMAIL_ERROR:			return 'Address is invalid';											break;	// 128
		case ISEMAIL_TOOLONG:			return 'Address is too long';											break;	// 129
		case ISEMAIL_NOAT:			return 'Address has no @ sign';											break;	// 130
		case ISEMAIL_NOLOCALPART:		return 'Address has no local part';										break;	// 131
		case ISEMAIL_NODOMAIN:			return 'Address has no domain part';										break;	// 132
		case ISEMAIL_ZEROLENGTHELEMENT:		return 'Address has an illegal zero-length element (starts or ends with a dot or has two dots together)';	break;	// 133
		case ISEMAIL_BADCOMMENT_START:		return 'Address contains illegal characters in a comment';							break;	// 134
		case ISEMAIL_BADCOMMENT_END:		return 'Address contains illegal characters in a comment';							break;	// 135
		case ISEMAIL_UNESCAPEDDELIM:		return 'Address contains an character that must be escaped but isn\'t';						break;	// 136
		case ISEMAIL_EMPTYELEMENT:		return 'Address has an illegal zero-length element (starts or ends with a dot or has two dots together)';	break;	// 137
		case ISEMAIL_UNESCAPEDSPECIAL:		return 'Address contains an character that must be escaped but isn\'t';						break;	// 138
		case ISEMAIL_LOCALTOOLONG:		return 'The local part of the address is too long';								break;	// 139
//		case ISEMAIL_IPV4BADPREFIX:		return 'The literal address contains an IPv4 address that is prefixed wrongly';					break;	// 140
		case ISEMAIL_IPV6BADPREFIXMIXED:	return 'The literal address is wrongly prefixed';								break;	// 141
		case ISEMAIL_IPV6BADPREFIX:		return 'The literal address is wrongly prefixed';								break;	// 142
		case ISEMAIL_IPV6GROUPCOUNT:		return 'The IPv6 literal address contains the wrong number of groups';						break;	// 143
		case ISEMAIL_IPV6DOUBLEDOUBLECOLON:	return 'The IPv6 literal address contains too many :: sequences';						break;	// 144
		case ISEMAIL_IPV6BADCHAR:		return 'The IPv6 address contains an illegal group of characters';						break;	// 145 Revision 2.8: text amended to more accurately reflect the error condition
		case ISEMAIL_IPV6TOOMANYGROUPS:		return 'The IPv6 address has too many groups';									break;	// 146
		case ISEMAIL_DOMAINEMPTYELEMENT:	return 'The domain part contains an empty element';								break;	// 147
		case ISEMAIL_DOMAINELEMENTTOOLONG:	return 'The domain part contains an element that is too long';							break;	// 148
		case ISEMAIL_DOMAINBADCHAR:		return 'The domain part contains an illegal character';								break;	// 149
		case ISEMAIL_DOMAINTOOLONG:		return 'The domain part is too long';										break;	// 150
		case ISEMAIL_IPV6SINGLECOLONSTART:	return 'IPv6 address starts with a single colon';								break;	// 151
		case ISEMAIL_IPV6SINGLECOLONEND:	return 'IPv6 address ends with a single colon';									break;	// 152
		// Unexpected errors
//		case ISEMAIL_BADPARAMETER:		return 'Unrecognised parameter';										break;	// 190
		default:				return 'Undefined error';												// 191 and others
		}
	case ISEMAIL_STATUSTEXT_CONSTANT:
		switch ($status) {
		case ISEMAIL_VALID:			return 'ISEMAIL_VALID';			break;	// 0
		// Warnings (valid address but unlikely in the real world)
		case ISEMAIL_WARNING:			return 'ISEMAIL_WARNING';		break;	// 64
		case ISEMAIL_TLD:			return 'ISEMAIL_TLD';			break;	// 65
		case ISEMAIL_TLDNUMERIC:		return 'ISEMAIL_TLDNUMERIC';		break;	// 66
		case ISEMAIL_QUOTEDSTRING:		return 'ISEMAIL_QUOTEDSTRING';		break;	// 67
		case ISEMAIL_COMMENTS:			return 'ISEMAIL_COMMENTS';		break;	// 68
		case ISEMAIL_FWS:			return 'ISEMAIL_FWS';			break;	// 69
		case ISEMAIL_ADDRESSLITERAL:		return 'ISEMAIL_ADDRESSLITERAL';	break;	// 70
		case ISEMAIL_UNLIKELYINITIAL:		return 'ISEMAIL_UNLIKELYINITIAL';	break;	// 71
		case ISEMAIL_SINGLEGROUPELISION:	return 'ISEMAIL_SINGLEGROUPELISION';	break;	// 72
		case ISEMAIL_DOMAINNOTFOUND:		return 'ISEMAIL_DOMAINNOTFOUND';	break;	// 73
		case ISEMAIL_MXNOTFOUND:		return 'ISEMAIL_MXNOTFOUND';		break;	// 74
		// Errors (invalid address)
		case ISEMAIL_ERROR:			return 'ISEMAIL_ERROR';			break;	// 128
		case ISEMAIL_TOOLONG:			return 'ISEMAIL_TOOLONG';		break;	// 129
		case ISEMAIL_NOAT:			return 'ISEMAIL_NOAT';			break;	// 130
		case ISEMAIL_NOLOCALPART:		return 'ISEMAIL_NOLOCALPART';		break;	// 131
		case ISEMAIL_NODOMAIN:			return 'ISEMAIL_NODOMAIN';		break;	// 132
		case ISEMAIL_ZEROLENGTHELEMENT:		return 'ISEMAIL_ZEROLENGTHELEMENT';	break;	// 133
		case ISEMAIL_BADCOMMENT_START:		return 'ISEMAIL_BADCOMMENT_START';	break;	// 134
		case ISEMAIL_BADCOMMENT_END:		return 'ISEMAIL_BADCOMMENT_END';	break;	// 135
		case ISEMAIL_UNESCAPEDDELIM:		return 'ISEMAIL_UNESCAPEDDELIM';	break;	// 136 fixed in version 2.6
		case ISEMAIL_EMPTYELEMENT:		return 'ISEMAIL_EMPTYELEMENT';		break;	// 137 fixed in version 2.6
		case ISEMAIL_UNESCAPEDSPECIAL:		return 'ISEMAIL_UNESCAPEDSPECIAL';	break;	// 138 fixed in version 2.6
		case ISEMAIL_LOCALTOOLONG:		return 'ISEMAIL_LOCALTOOLONG';		break;	// 139
//		case ISEMAIL_IPV4BADPREFIX:		return 'ISEMAIL_IPV4BADPREFIX';		break;	// 140
		case ISEMAIL_IPV6BADPREFIXMIXED:	return 'ISEMAIL_IPV6BADPREFIXMIXED';	break;	// 141
		case ISEMAIL_IPV6BADPREFIX:		return 'ISEMAIL_IPV6BADPREFIX';		break;	// 142
		case ISEMAIL_IPV6GROUPCOUNT:		return 'ISEMAIL_IPV6GROUPCOUNT';	break;	// 143
		case ISEMAIL_IPV6DOUBLEDOUBLECOLON:	return 'ISEMAIL_IPV6DOUBLEDOUBLECOLON';	break;	// 144
		case ISEMAIL_IPV6BADCHAR:		return 'ISEMAIL_IPV6BADCHAR';		break;	// 145
		case ISEMAIL_IPV6TOOMANYGROUPS:		return 'ISEMAIL_IPV6TOOMANYGROUPS';	break;	// 146
		case ISEMAIL_DOMAINEMPTYELEMENT:	return 'ISEMAIL_DOMAINEMPTYELEMENT';	break;	// 147
		case ISEMAIL_DOMAINELEMENTTOOLONG:	return 'ISEMAIL_DOMAINELEMENTTOOLONG';	break;	// 148
		case ISEMAIL_DOMAINBADCHAR:		return 'ISEMAIL_DOMAINBADCHAR';		break;	// 149
		case ISEMAIL_DOMAINTOOLONG:		return 'ISEMAIL_DOMAINTOOLONG';		break;	// 150
		case ISEMAIL_IPV6SINGLECOLONSTART:	return 'ISEMAIL_IPV6SINGLECOLONSTART';	break;	// 151
		case ISEMAIL_IPV6SINGLECOLONEND:	return 'ISEMAIL_IPV6SINGLECOLONEND';	break;	// 152
		// Unexpected errors
//		case ISEMAIL_BADPARAMETER:		return 'ISEMAIL_BADPARAMETER';		break;	// 190
		default:				return 'Unknown constant';			// 191 and others
		}
	case ISEMAIL_STATUSTEXT_SMTPCODE:
		// These codes assume we are validating a recipient address
		// The correct use of reply code 553 is documented in RFCs 821, 2821 & 5321.
		//	http://tools.ietf.org/html/rfc5321#section-4.2

		// The SMTP enhanced status codes (5.1.x) are maintained in the IANA registry
		// 	http://www.iana.org/assignments/smtp-enhanced-status-codes
		// as defined in RFC 5428.
		if ($status < ISEMAIL_ERROR) {
			return ISEMAIL_STATUSTEXT_SMTP_250_215;
		} else {
			switch ($status) {
			// Errors (invalid address)
			case ISEMAIL_TOOLONG:			return ISEMAIL_STATUSTEXT_SMTP_553_513;	break;	// 129
			case ISEMAIL_NOAT:			return ISEMAIL_STATUSTEXT_SMTP_553_513;	break;	// 130
			case ISEMAIL_NOLOCALPART:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 131
			case ISEMAIL_NODOMAIN:			return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 132
			case ISEMAIL_ZEROLENGTHELEMENT:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 133
			case ISEMAIL_BADCOMMENT_START:		return ISEMAIL_STATUSTEXT_SMTP_553_513;	break;	// 134
			case ISEMAIL_BADCOMMENT_END:		return ISEMAIL_STATUSTEXT_SMTP_553_513;	break;	// 135
			case ISEMAIL_UNESCAPEDDELIM:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 136 fixed in version 2.6
			case ISEMAIL_EMPTYELEMENT:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 137 fixed in version 2.6
			case ISEMAIL_UNESCAPEDSPECIAL:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 138 fixed in version 2.6
			case ISEMAIL_LOCALTOOLONG:		return ISEMAIL_STATUSTEXT_SMTP_553_511;	break;	// 139
//			case ISEMAIL_IPV4BADPREFIX:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 140
			case ISEMAIL_IPV6BADPREFIXMIXED:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 141
			case ISEMAIL_IPV6BADPREFIX:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 142
			case ISEMAIL_IPV6GROUPCOUNT:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 143
			case ISEMAIL_IPV6DOUBLEDOUBLECOLON:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 144
			case ISEMAIL_IPV6BADCHAR:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 145
			case ISEMAIL_IPV6TOOMANYGROUPS:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 146
			case ISEMAIL_DOMAINEMPTYELEMENT:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 147
			case ISEMAIL_DOMAINELEMENTTOOLONG:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 148
			case ISEMAIL_DOMAINBADCHAR:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 149
			case ISEMAIL_DOMAINTOOLONG:		return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 150
			case ISEMAIL_IPV6SINGLECOLONSTART:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 151
			case ISEMAIL_IPV6SINGLECOLONEND:	return ISEMAIL_STATUSTEXT_SMTP_553_512;	break;	// 152
			// Unexpected errors
			default:				return ISEMAIL_STATUSTEXT_SMTP_553_510;		// 128, 191 and others
			}
		}
	default:
		return "Status is $status. Unknown status text type: passed as " . gettype($type) . ' with value "' . strval($type) . '"';
	}
}
?>
