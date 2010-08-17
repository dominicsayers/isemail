<?php
/*
 * Return a text status message depending on the is_email() return status
 */
/*.string.*/ function is_email_statustext(/*.integer.*/ $status) {
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
		case ISEMAIL_DOMAINNOTFOUND:		return 'Couldn\'t find an A record for this domain';								break;	// 73
		case ISEMAIL_MXNOTFOUND:		return 'Couldn\'t find an MX record for this domain';								break;	// 74
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
		case ISEMAIL_IPV4BADPREFIX:		return 'The literal address contains an IPv4 address that is prefixed wrongly';					break;	// 140
		case ISEMAIL_IPV6BADPREFIXMIXED:	return 'The literal address is wrongly prefixed';								break;	// 141
		case ISEMAIL_IPV6BADPREFIX:		return 'The literal address is wrongly prefixed';								break;	// 142
		case ISEMAIL_IPV6GROUPCOUNT:		return 'The IPv6 literal address contains the wrong number of groups';						break;	// 143
		case ISEMAIL_IPV6DOUBLEDOUBLECOLON:	return 'The IPv6 literal address contains too many :: sequences';						break;	// 144
		case ISEMAIL_IPV6BADCHAR:		return 'The IPv6 address contains an illegal character';							break;	// 145
		case ISEMAIL_IPV6TOOMANYGROUPS:		return 'The IPv6 address has too many groups';									break;	// 146
		case ISEMAIL_DOMAINEMPTYELEMENT:	return 'The domain part contains an empty element';								break;	// 147
		case ISEMAIL_DOMAINELEMENTTOOLONG:	return 'The domain part contains an element that is too long';							break;	// 148
		case ISEMAIL_DOMAINBADCHAR:		return 'The domain part contains an illegal character';								break;	// 149
		case ISEMAIL_DOMAINTOOLONG:		return 'The domain part is too long';										break;	// 150
		case ISEMAIL_IPV6SINGLECOLONSTART:	return 'IPv6 address starts with a single colon';								break;	// 151
		case ISEMAIL_IPV6SINGLECOLONEND:	return 'IPv6 address ends with a single colon';									break;	// 152
		// Unexpected errors
		case ISEMAIL_BADPARAMETER:		return 'Unrecognised parameter';										break;	// 190
		default:				return 'Undefined error';												// 191 and others
	}
}
?>