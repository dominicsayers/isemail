<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>is_email() - Run unit tests</title>

<style type="text/css">
div {clear:left;}
p {font-family:Segoe UI,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;float:left;}
p.valid {width:60px;}
p.address {text-align:right;width:400px;overflow:hidden;margin-right:8px;}
p.author {font-style:italic;}
hr {clear:left;}
</style>
</head>

<body>
<?php
require_once '../is_email.php';

function unitTest ($email, $expected, $comment = '') {
	$valid		= is_email($email);
	$not		= ($valid) ? 'Valid' : 'Not valid';
	$unexpected	= ($valid !== $expected) ? " <b>$not</b>" : "$not";
	$comment		= ($comment === '') ? "&nbsp;" : stripslashes("$comment");
	
	return "<div><p class=\"address\"<em>$email</em></p><p class=\"valid\">$unexpected</p><p class=\"comment\">$comment</p></div>
";
}

echo "<h3>Email address validation test suite version 1.8</h3>\n";
echo "<p class=\"author\">Dominic Sayers | <a href=\"mailto:dominic_sayers@hotmail.com\">dominic_sayers@hotmail.com</a> | <a href=\"http://www.dominicsayers.com/isemail\">RFC-compliant email address validation</a></p>\n<br>\n<hr>\n";
echo unitTest("first.last@example.com", true, "");
echo unitTest("1234567890123456789012345678901234567890123456789012345678901234@example.com", true, "");
echo unitTest("first.last@sub.do,com", false, "Mistyped comma instead of dot (replaces old #3 which was the same as #57)");
echo unitTest("\"first\\\"last\"@example.com", true, "");
echo unitTest("first\\@last@example.com", false, "Escaping can only happen within a quoted string");
echo unitTest("\"first@last\"@example.com", true, "");
echo unitTest("\"first\\\\last\"@example.com", true, "");
echo unitTest("x@x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x234", true, "");
echo unitTest("123456789012345678901234567890123456789012345678901234567890@12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.123456789012345678901234567890123456789012345678901234567890123.example.com", true, "");
echo unitTest("first.last@[12.34.56.78]", true, "");
echo unitTest("first.last@[IPv6:::12.34.56.78]", true, "");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:12.34.56.78]", true, "");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:12.34.56.78]", true, "");
echo unitTest("first.last@[IPv6:::1111:2222:3333:4444:5555:6666]", true, "");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:6666]", true, "");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666::]", true, "");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:8888]", true, "");
echo unitTest("first.last@x23456789012345678901234567890123456789012345678901234567890123.example.com", true, "");
echo unitTest("first.last@1xample.com", true, "");
echo unitTest("first.last@123.example.com", true, "");
echo unitTest("123456789012345678901234567890123456789012345678901234567890@12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.1234.example.com", false, "Entire address is longer than 256 characters");
echo unitTest("first.last", false, "No @");
echo unitTest("12345678901234567890123456789012345678901234567890123456789012345@example.com", false, "Local part more than 64 characters");
echo unitTest(".first.last@example.com", false, "Local part starts with a dot");
echo unitTest("first.last.@example.com", false, "Local part ends with a dot");
echo unitTest("first..last@example.com", false, "Local part has consecutive dots");
echo unitTest("\"first\"last\"@example.com", false, "Local part contains unescaped excluded characters");
echo unitTest("\"first\\last\"@example.com", true, "Any character can be escaped in a quoted string");
echo unitTest("\"\"\"@example.com", false, "Local part contains unescaped excluded characters");
echo unitTest("\"\\\"@example.com", false, "Local part cannot end with a backslash");
echo unitTest("\"\"@example.com", false, "Local part is effectively empty");
echo unitTest("first\\\\@last@example.com", false, "Local part contains unescaped excluded characters");
echo unitTest("first.last@", false, "No domain");
echo unitTest("x@x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456", false, "Domain exceeds 255 chars");
echo unitTest("first.last@[.12.34.56.78]", false, "Only char that can precede IPv4 address is \':\'");
echo unitTest("first.last@[12.34.56.789]", false, "Can\'t be interpreted as IPv4 so IPv6 tag is missing");
echo unitTest("first.last@[::12.34.56.78]", false, "IPv6 tag is missing");
echo unitTest("first.last@[IPv5:::12.34.56.78]", false, "IPv6 tag is wrong");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:12.34.56.78]", false, "Too many IPv6 groups (4 max)");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:12.34.56.78]", false, "Not enough IPv6 groups");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:12.34.56.78]", false, "Too many IPv6 groups (6 max)");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777]", false, "Not enough IPv6 groups");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:8888:9999]", false, "Too many IPv6 groups (8 max)");
echo unitTest("first.last@[IPv6:1111:2222::3333::4444:5555:6666]", false, "Too many \'::\' (can be none or one)");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:6666:7777]", false, "Too many IPv6 groups (6 max)");
echo unitTest("first.last@[IPv6:1111:2222:333x::4444:5555]", false, "x is not valid in an IPv6 address");
echo unitTest("first.last@[IPv6:1111:2222:33333::4444:5555]", false, "33333 is not a valid group in an IPv6 address");
echo unitTest("first.last@example.123", false, "TLD can\'t be all digits");
echo unitTest("first.last@com", false, "Mail host must be second- or lower level");
echo unitTest("first.last@-xample.com", false, "Label can\'t begin with a hyphen");
echo unitTest("first.last@exampl-.com", false, "Label can\'t end with a hyphen");
echo unitTest("first.last@x234567890123456789012345678901234567890123456789012345678901234.example.com", false, "Label can\'t be longer than 63 octets");
echo unitTest("\"Abc\\@def\"@example.com", true, "");
echo unitTest("\"Fred\\ Bloggs\"@example.com", true, "");
echo unitTest("\"Joe.\\\\Blow\"@example.com", true, "");
echo unitTest("\"Abc@def\"@example.com", true, "");
echo unitTest("\"Fred Bloggs\"@example.com", true, "");
echo unitTest("user+mailbox@example.com", true, "");
echo unitTest("customer/department=shipping@example.com", true, "");
echo unitTest("\$A12345@example.com", true, "");
echo unitTest("!def!xyz%abc@example.com", true, "");
echo unitTest("_somename@example.com", true, "");
echo unitTest("dclo@us.ibm.com", true, "");
echo unitTest("abc\\@def@example.com", false, "This example from RFC3696 was corrected in an erratum");
echo unitTest("abc\\\\@example.com", false, "This example from RFC3696 was corrected in an erratum");
echo unitTest("peter.piper@example.com", true, "");
echo unitTest("Doug\\ \\\"Ace\\\"\\ Lovell@example.com", false, "Escaping can only happen in a quoted string");
echo unitTest("\"Doug \\\"Ace\\\" L.\"@example.com", true, "");
echo unitTest("abc@def@example.com", false, "Doug Lovell says this should fail");
echo unitTest("abc\\\\@def@example.com", false, "Doug Lovell says this should fail");
echo unitTest("abc\\@example.com", false, "Doug Lovell says this should fail");
echo unitTest("@example.com", false, "No local part");
echo unitTest("doug@", false, "Doug Lovell says this should fail");
echo unitTest("\"qu@example.com", false, "Doug Lovell says this should fail");
echo unitTest("ote\"@example.com", false, "Doug Lovell says this should fail");
echo unitTest(".dot@example.com", false, "Doug Lovell says this should fail");
echo unitTest("dot.@example.com", false, "Doug Lovell says this should fail");
echo unitTest("two..dot@example.com", false, "Doug Lovell says this should fail");
echo unitTest("\"Doug \"Ace\" L.\"@example.com", false, "Doug Lovell says this should fail");
echo unitTest("Doug\\ \\\"Ace\\\"\\ L\\.@example.com", false, "Doug Lovell says this should fail");
echo unitTest("hello world@example.com", false, "Doug Lovell says this should fail");
echo unitTest("gatsby@f.sc.ot.t.f.i.tzg.era.l.d.", false, "Doug Lovell says this should fail");
echo unitTest("test@example.com", true, "");
echo unitTest("TEST@example.com", true, "");
echo unitTest("1234567890@example.com", true, "");
echo unitTest("test+test@example.com", true, "");
echo unitTest("test-test@example.com", true, "");
echo unitTest("t*est@example.com", true, "");
echo unitTest("+1~1+@example.com", true, "");
echo unitTest("{_test_}@example.com", true, "");
echo unitTest("\"[[ test ]]\"@example.com", true, "");
echo unitTest("test.test@example.com", true, "");
echo unitTest("\"test.test\"@example.com", true, "");
echo unitTest("test.\"test\"@example.com", true, "Obsolete form, but documented in RFC2822");
echo unitTest("\"test@test\"@example.com", true, "");
echo unitTest("test@123.123.123.x123", true, "");
echo unitTest("test@123.123.123.123", false, "Top Level Domain won\'t be all-numeric (see RFC3696 Section 2). I disagree with Dave Child on this one.");
echo unitTest("test@[123.123.123.123]", true, "");
echo unitTest("test@example.example.com", true, "");
echo unitTest("test@example.example.example.com", true, "");
echo unitTest("test.example.com", false, "");
echo unitTest("test.@example.com", false, "");
echo unitTest("test..test@example.com", false, "");
echo unitTest(".test@example.com", false, "");
echo unitTest("test@test@example.com", false, "");
echo unitTest("test@@example.com", false, "");
echo unitTest("-- test --@example.com", false, "No spaces allowed in local part");
echo unitTest("[test]@example.com", false, "Square brackets only allowed within quotes");
echo unitTest("\"test\\test\"@example.com", true, "Any character can be escaped in a quoted string");
echo unitTest("\"test\"test\"@example.com", false, "Quotes cannot be nested");
echo unitTest("()[]\\;:,><@example.com", false, "Disallowed Characters");
echo unitTest("test@.", false, "Dave Child says so");
echo unitTest("test@example.", false, "Dave Child says so");
echo unitTest("test@.org", false, "Dave Child says so");
echo unitTest("test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com", false, "255 characters is maximum length for domain. This is 256.");
echo unitTest("test@example", false, "Dave Child says so");
echo unitTest("test@[123.123.123.123", false, "Dave Child says so");
echo unitTest("test@123.123.123.123]", false, "Dave Child says so");
echo unitTest("NotAnEmail", false, "Phil Haack says so");
echo unitTest("@NotAnEmail", false, "Phil Haack says so");
echo unitTest("\"test\\\\blah\"@example.com", true, "");
echo unitTest("\"test\\blah\"@example.com", true, "Any character can be escaped in a quoted string");
echo unitTest("\"test\\\rblah\"@example.com", true, "Quoted string specifically excludes carriage returns unless escaped");
echo unitTest("\"test\rblah\"@example.com", false, "Quoted string specifically excludes carriage returns");
echo unitTest("\"test\\\"blah\"@example.com", true, "");
echo unitTest("\"test\"blah\"@example.com", false, "Phil Haack says so");
echo unitTest("customer/department@example.com", true, "");
echo unitTest("_Yosemite.Sam@example.com", true, "");
echo unitTest("~@example.com", true, "");
echo unitTest(".wooly@example.com", false, "Phil Haack says so");
echo unitTest("wo..oly@example.com", false, "Phil Haack says so");
echo unitTest("pootietang.@example.com", false, "Phil Haack says so");
echo unitTest(".@example.com", false, "Phil Haack says so");
echo unitTest("\"Austin@Powers\"@example.com", true, "");
echo unitTest("Ima.Fool@example.com", true, "");
echo unitTest("\"Ima.Fool\"@example.com", true, "");
echo unitTest("\"Ima Fool\"@example.com", true, "");
echo unitTest("Ima Fool@example.com", false, "Phil Haack says so");
echo unitTest("phil.h\\@\\@ck@haacked.com", false, "Escaping can only happen in a quoted string");
echo unitTest("\"first\".\"last\"@example.com", true, "");
echo unitTest("\"first\".middle.\"last\"@example.com", true, "");
echo unitTest("\"first\\\\\"last\"@example.com", false, "Contains an unescaped quote");
echo unitTest("\"first\".last@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("first.\"last\"@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("\"first\".\"middle\".\"last\"@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("\"first.middle\".\"last\"@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("\"first.middle.last\"@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("\"first..last\"@example.com", true, "obs-local-part form as described in RFC 2822");
echo unitTest("foo@[\\1.2.3.4]", false, "RFC 5321 specifies the syntax for address-literal and does not allow escaping");
echo unitTest("\"first\\\\\\\"last\"@example.com", true, "");
echo unitTest("first.\"mid\\dle\".\"last\"@example.com", true, "Backslash can escape anything but must escape something");
echo unitTest("Test.\r\n Folding.\r\n Whitespace@example.com", true, "");
echo unitTest("first.\"\".last@example.com", false, "Contains a zero-length element");
echo unitTest("first\\last@example.com", false, "Unquoted string must be an atom");
echo unitTest("Abc\\@def@example.com", false, "Was incorrectly given as a valid address in the original RFC3696");
echo unitTest("Fred\\ Bloggs@example.com", false, "Was incorrectly given as a valid address in the original RFC3696");
echo unitTest("Joe.\\\\Blow@example.com", false, "Was incorrectly given as a valid address in the original RFC3696");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:12.34.567.89]", false, "IPv4 part contains an invalid octet");
echo unitTest("\"test\\\r\n blah\"@example.com", false, "Folding white space can\'t appear within a quoted pair");
echo unitTest("\"test\r\n blah\"@example.com", true, "This is a valid quoted string with folding white space");
echo unitTest("{^c\\@**Dog^}@cartoon.com", false, "This is a throwaway example from Doug Lovell\'s article. Actually it\'s not a valid address.");
echo unitTest("(foo)cal(bar)@(baz)iamcal.com(quux)", true, "A valid address containing comments");
echo unitTest("cal@iamcal(woo).(yay)com", true, "A valid address containing comments");
echo unitTest("\"foo\"(yay)@(hoopla)[1.2.3.4]", false, "Address literal can\'t be commented (RFC5321)");
echo unitTest("cal(woo(yay)hoopla)@iamcal.com", true, "A valid address containing comments");
echo unitTest("cal(foo\\@bar)@iamcal.com", true, "A valid address containing comments");
echo unitTest("cal(foo\\)bar)@iamcal.com", true, "A valid address containing comments and an escaped parenthesis");
echo unitTest("cal(foo(bar)@iamcal.com", false, "Unclosed parenthesis in comment");
echo unitTest("cal(foo)bar)@iamcal.com", false, "Too many closing parentheses");
echo unitTest("cal(foo\\)@iamcal.com", false, "Backslash at end of comment has nothing to escape");
echo unitTest("first().last@example.com", true, "A valid address containing an empty comment");
echo unitTest("first.(\r\n middle\r\n )last@example.com", true, "Comment with folding white space");
echo unitTest("first(12345678901234567890123456789012345678901234567890)last@(1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890)example.com", false, "Too long with comments, not too long without");
echo unitTest("first(Welcome to\r\n the (\"wonderful\" (!)) world\r\n of email)@example.com", true, "Silly example from my blog post");
echo unitTest("pete(his account)@silly.test(his host)", true, "Canonical example from RFC5322");
echo unitTest("c@(Chris\'s host.)public.example", true, "Canonical example from RFC5322");
echo unitTest("jdoe@machine(comment).  example", true, "Canonical example from RFC5322");
echo unitTest("1234   @   local(blah)  .machine .example", true, "Canonical example from RFC5322");
echo unitTest("first(middle)last@example.com", false, "Can\'t have a comment or white space except at an element boundary");
echo unitTest("first(abc.def).last@example.com", true, "Comment can contain a dot");
echo unitTest("first(a\"bc.def).last@example.com", true, "Comment can contain double quote");
echo unitTest("first.(\")middle.last(\")@example.com", true, "Comment can contain a quote");
echo unitTest("first(abc(\"def\".ghi).mno)middle(abc(\"def\".ghi).mno).last@(abc(\"def\".ghi).mno)example(abc(\"def\".ghi).mno).(abc(\"def\".ghi).mno)com(abc(\"def\".ghi).mno)", false, "Can\'t have comments or white space except at an element boundary");
echo unitTest("first(abc\\(def)@example.com", true, "Comment can contain quoted-pair");
echo unitTest("first.last@x(1234567890123456789012345678901234567890123456789012345678901234567890).com", true, "Label is longer than 63 octets, but not with comment removed");
echo unitTest("a(a(b(c)d(e(f))g)h(i)j)@example.com", true, "");
echo unitTest("a(a(b(c)d(e(f))g)(h(i)j)@example.com", false, "Braces are not properly matched");
echo unitTest("name.lastname@domain.com", true, "");
echo unitTest(".@", false, "");
echo unitTest("a@b", false, "");
echo unitTest("@bar.com", false, "");
echo unitTest("@@bar.com", false, "");
echo unitTest("a@bar.com", true, "");
echo unitTest("aaa.com", false, "");
echo unitTest("aaa@.com", false, "");
echo unitTest("aaa@.123", false, "");
echo unitTest("aaa@[123.123.123.123]", true, "");
echo unitTest("aaa@[123.123.123.123]a", false, "extra data outside ip");
echo unitTest("aaa@[123.123.123.333]", false, "not a valid IP");
echo unitTest("a@bar.com.", false, "");
echo unitTest("a@bar", false, "");
echo unitTest("a-b@bar.com", true, "");
echo unitTest("+@b.c", true, "TLDs can be any length");
echo unitTest("+@b.com", true, "");
echo unitTest("a@-b.com", false, "");
echo unitTest("a@b-.com", false, "");
echo unitTest("-@..com", false, "");
echo unitTest("-@a..com", false, "");
echo unitTest("a@b.co-foo.uk", true, "");
echo unitTest("\"hello my name is\"@stutter.com", true, "");
echo unitTest("\"Test \\\"Fail\\\" Ing\"@example.com", true, "");
echo unitTest("valid@special.museum", true, "");
echo unitTest("invalid@special.museum-", false, "");
echo unitTest("shaitan@my-domain.thisisminekthx", true, "Disagree with Paul Gregg here");
echo unitTest("test@...........com", false, "......");
echo unitTest("foobar@192.168.0.1", false, "ip need to be []");
echo unitTest("\"Joe\\\\Blow\"@example.com", true, "");
echo unitTest("Invalid \\\n Folding \\\n Whitespace@example.com", false, "This isn\'t FWS so Dominic Sayers says it\'s invalid");
echo unitTest("HM2Kinsists@(that comments are allowed)this.is.ok", true, "");
echo unitTest("user%uucp!path@somehost.edu", true, "");
echo unitTest("\"first(last)\"@example.com", true, "");
echo unitTest(" \r\n (\r\n x \r\n ) \r\n first\r\n ( \r\n x\r\n ) \r\n .\r\n ( \r\n x) \r\n last \r\n (  x \r\n ) \r\n @example.com", true, "");
echo unitTest("test. \r\n \r\n obs@syntax.com", true, "obs-fws allows multiple lines");
echo unitTest("test. \r\n \r\n obs@syntax.com", true, "obs-fws allows multiple lines (test 2: space before break)");
echo unitTest("test.\r\n\r\n obs@syntax.com", false, "obs-fws must have at least one WSP per line");
echo unitTest("\"null \\\0\"@char.com", true, "can have escaped null character");
echo unitTest("\"null \0\"@char.com", false, "cannot have unescaped null character");
echo unitTest("null\\\0@char.com", false, "escaped null must be in quoted string");
?>
</body>

</html>