<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>devpkg() - Run unit tests</title>

<style type="text/css">
div {clear:left;}
p {font-family:Segoe UI,Arial,Helvetica,sans-serif;font-size:12px;margin:0;padding:0;float:left;}
p.valid {width:90px;}
p.warning {width:90px;}
p.diagnosis {width:90px;}
p.address {text-align:right;width:400px;overflow:hidden;margin-right:8px;}
p.id {text-align:right;width:40px;overflow:hidden;margin-right:8px;}
p.author {font-style:italic;}
hr {clear:left;}
</style>
</head>

<body>
<?php
require_once '../devpkg.php';
require_once '../extras/is_email_statustext.php';

function unitTest ($email, $expected, $warn_expected, $comment = '', $id = '') {
	$diagnosis	= devpkg($email, false, true);
	$text		= is_email_statustext($diagnosis);

	$warn		= (($diagnosis & ISEMAIL_WARNING) !== 0);
	$valid		= ($diagnosis < ISEMAIL_ERROR);

	$warning	= ($warn) ? $diagnosis : '&nbsp;';
	$result		= ($valid) ? 'Valid' : 'Not valid';

	if ($valid	!== $expected)		$result		= "<strong>$result</strong>";
	if ($warn	!== $warn_expected)	$warning	= "<strong>$warning</strong>";

	$comment	= stripslashes($comment);

	if ($text !== '')	$comment .= ($comment === '') ? stripslashes($text) : ' (' . stripslashes($text) . ')';
	if ($comment === '')	$comment = "&nbsp;";

	return "<div><p class=\"address\"<em>$email</em></p><p class=\"id\">$id</p><p class=\"valid\">$result</p><p class=\"warning\">$warning</p><p class=\"diagnosis\">$diagnosis</p><p class=\"comment\">$comment</p></div>\n";
}

echo "<h3>Email address validation test suite version 2.1</h3>\n";
echo "<p class=\"author\">Dominic Sayers | <a href=\"mailto:dominic@sayers.cc\">dominic@sayers.cc</a> | <a href=\"http://www.dominicsayers.com/isemail\">RFC-compliant email address validation</a></p>\n<br>\n<hr>\n";
echo "<div><p class=\"address\"<strong>Address</strong></p><p class=\"id\"><strong>Test #</strong></p><p class=\"valid\"><strong>Result</strong></p><p class=\"warning\"><strong>Warning</strong></p><p class=\"diagnosis\"><strong>Diagnosis</strong></p><p class=\"comment\"><strong>Comment</strong></p></div>\n";echo unitTest("first.last@example.com", true, false, "", "1");
echo unitTest("1234567890123456789012345678901234567890123456789012345678901234@example.com", true, false, "", "2");
echo unitTest("first.last@sub.do,com", false, false, "Mistyped comma instead of dot (replaces old #3 which was the same as #57)", "3");
echo unitTest("\"first\\\"last\"@example.com", true, true, "", "4");
echo unitTest("first\\@last@example.com", false, false, "Escaping can only happen within a quoted string", "5");
echo unitTest("\"first@last\"@example.com", true, true, "", "6");
echo unitTest("\"first\\\\last\"@example.com", true, true, "", "7");
echo unitTest("x@x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x2", true, false, "Total length reduced to 254 characters so it's still valid", "8");
echo unitTest("1234567890123456789012345678901234567890123456789012345678@12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.123456789012345678901234567890123456789012345678901234567890123.example.com", true, false, "Total length reduced to 254 characters so it's still valid", "9");
echo unitTest("first.last@[12.34.56.78]", true, true, "", "10");
echo unitTest("first.last@[IPv6:::12.34.56.78]", true, true, "", "11");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:12.34.56.78]", true, true, "", "12");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:12.34.56.78]", true, true, "", "13");
echo unitTest("first.last@[IPv6:::1111:2222:3333:4444:5555:6666]", true, true, "", "14");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:6666]", true, true, "", "15");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666::]", true, true, "", "16");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:8888]", true, true, "", "17");
echo unitTest("first.last@x23456789012345678901234567890123456789012345678901234567890123.example.com", true, false, "", "18");
echo unitTest("first.last@1xample.com", true, false, "", "19");
echo unitTest("first.last@123.example.com", true, false, "", "20");
echo unitTest("123456789012345678901234567890123456789012345678901234567890@12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.12345678901234567890123456789012345678901234567890123456789.12.example.com", false, false, "Entire address is longer than 254 characters", "21");
echo unitTest("first.last", false, false, "No @", "22");
echo unitTest("12345678901234567890123456789012345678901234567890123456789012345@example.com", false, false, "Local part more than 64 characters", "23");
echo unitTest(".first.last@example.com", false, false, "Local part starts with a dot", "24");
echo unitTest("first.last.@example.com", false, false, "Local part ends with a dot", "25");
echo unitTest("first..last@example.com", false, false, "Local part has consecutive dots", "26");
echo unitTest("\"first\"last\"@example.com", false, false, "Local part contains unescaped excluded characters", "27");
echo unitTest("\"first\\last\"@example.com", true, true, "Any character can be escaped in a quoted string", "28");
echo unitTest("\"\"\"@example.com", false, false, "Local part contains unescaped excluded characters", "29");
echo unitTest("\"\\\"@example.com", false, false, "Local part cannot end with a backslash", "30");
echo unitTest("\"\"@example.com", false, false, "Local part is effectively empty", "31");
echo unitTest("first\\\\@last@example.com", false, false, "Local part contains unescaped excluded characters", "32");
echo unitTest("first.last@", false, false, "No domain", "33");
echo unitTest("x@x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456789.x23456", false, false, "Domain exceeds 255 chars", "34");
echo unitTest("first.last@[.12.34.56.78]", false, false, "Only char that can precede IPv4 address is ':'", "35");
echo unitTest("first.last@[12.34.56.789]", false, false, "Can't be interpreted as IPv4 so IPv6 tag is missing", "36");
echo unitTest("first.last@[::12.34.56.78]", false, false, "IPv6 tag is missing", "37");
echo unitTest("first.last@[IPv5:::12.34.56.78]", false, false, "IPv6 tag is wrong", "38");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:12.34.56.78]", true, true, "RFC 4291 disagrees with RFC 5321 but is cited by it", "39");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:12.34.56.78]", false, false, "Not enough IPv6 groups", "40");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:12.34.56.78]", false, false, "Too many IPv6 groups (6 max)", "41");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777]", false, false, "Not enough IPv6 groups", "42");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:7777:8888:9999]", false, false, "Too many IPv6 groups (8 max)", "43");
echo unitTest("first.last@[IPv6:1111:2222::3333::4444:5555:6666]", false, false, "Too many '::' (can be none or one)", "44");
echo unitTest("first.last@[IPv6:1111:2222:3333::4444:5555:6666:7777]", true, true, "RFC 4291 disagrees with RFC 5321 but is cited by it", "45");
echo unitTest("first.last@[IPv6:1111:2222:333x::4444:5555]", false, false, "x is not valid in an IPv6 address", "46");
echo unitTest("first.last@[IPv6:1111:2222:33333::4444:5555]", false, false, "33333 is not a valid group in an IPv6 address", "47");
echo unitTest("first.last@example.123", true, true, "TLD can't be all digits", "48");
echo unitTest("first.last@com", true, true, "Mail host must be second- or lower level", "49");
echo unitTest("first.last@-xample.com", false, false, "Label can't begin with a hyphen", "50");
echo unitTest("first.last@exampl-.com", false, false, "Label can't end with a hyphen", "51");
echo unitTest("first.last@x234567890123456789012345678901234567890123456789012345678901234.example.com", false, false, "Label can't be longer than 63 octets", "52");
echo unitTest("\"Abc\\@def\"@example.com", true, true, "", "53");
echo unitTest("\"Fred\\ Bloggs\"@example.com", true, true, "", "54");
echo unitTest("\"Joe.\\\\Blow\"@example.com", true, true, "", "55");
echo unitTest("\"Abc@def\"@example.com", true, true, "", "56");
echo unitTest("\"Fred Bloggs\"@example.com", true, true, "", "57");
echo unitTest("user+mailbox@example.com", true, false, "", "58");
echo unitTest("customer/department=shipping@example.com", true, false, "", "59");
echo unitTest("\$A12345@example.com", true, true, "", "60");
echo unitTest("!def!xyz%abc@example.com", true, true, "", "61");
echo unitTest("_somename@example.com", true, false, "", "62");
echo unitTest("dclo@us.ibm.com", true, false, "", "63");
echo unitTest("abc\\@def@example.com", false, false, "This example from RFC 3696 was corrected in an erratum", "64");
echo unitTest("abc\\\\@example.com", false, false, "This example from RFC 3696 was corrected in an erratum", "65");
echo unitTest("peter.piper@example.com", true, false, "", "66");
echo unitTest("Doug\\ \\\"Ace\\\"\\ Lovell@example.com", false, false, "Escaping can only happen in a quoted string", "67");
echo unitTest("\"Doug \\\"Ace\\\" L.\"@example.com", true, true, "", "68");
echo unitTest("abc@def@example.com", false, false, "Doug Lovell says this should fail", "69");
echo unitTest("abc\\\\@def@example.com", false, false, "Doug Lovell says this should fail", "70");
echo unitTest("abc\\@example.com", false, false, "Doug Lovell says this should fail", "71");
echo unitTest("@example.com", false, false, "No local part", "72");
echo unitTest("doug@", false, false, "Doug Lovell says this should fail", "73");
echo unitTest("\"qu@example.com", false, false, "Doug Lovell says this should fail", "74");
echo unitTest("ote\"@example.com", false, false, "Doug Lovell says this should fail", "75");
echo unitTest(".dot@example.com", false, false, "Doug Lovell says this should fail", "76");
echo unitTest("dot.@example.com", false, false, "Doug Lovell says this should fail", "77");
echo unitTest("two..dot@example.com", false, false, "Doug Lovell says this should fail", "78");
echo unitTest("\"Doug \"Ace\" L.\"@example.com", false, false, "Doug Lovell says this should fail", "79");
echo unitTest("Doug\\ \\\"Ace\\\"\\ L\\.@example.com", false, false, "Doug Lovell says this should fail", "80");
echo unitTest("hello world@example.com", false, false, "Doug Lovell says this should fail", "81");
echo unitTest("gatsby@f.sc.ot.t.f.i.tzg.era.l.d.", false, false, "Doug Lovell says this should fail", "82");
echo unitTest("test@example.com", true, false, "", "83");
echo unitTest("TEST@example.com", true, false, "", "84");
echo unitTest("1234567890@example.com", true, false, "", "85");
echo unitTest("test+test@example.com", true, false, "", "86");
echo unitTest("test-test@example.com", true, false, "", "87");
echo unitTest("t*est@example.com", true, false, "", "88");
echo unitTest("+1~1+@example.com", true, true, "", "89");
echo unitTest("{_test_}@example.com", true, true, "", "90");
echo unitTest("\"[[ test ]]\"@example.com", true, true, "", "91");
echo unitTest("test.test@example.com", true, false, "", "92");
echo unitTest("\"test.test\"@example.com", true, true, "", "93");
echo unitTest("test.\"test\"@example.com", true, true, "Obsolete form, but documented in RFC 5322", "94");
echo unitTest("\"test@test\"@example.com", true, true, "", "95");
echo unitTest("test@123.123.123.x123", true, false, "", "96");
echo unitTest("test@123.123.123.123", true, true, "Top Level Domain won't be all-numeric (see RFC 3696 Section 2). I disagree with Dave Child on this one.", "97");
echo unitTest("test@[123.123.123.123]", true, true, "", "98");
echo unitTest("test@example.example.com", true, false, "", "99");
echo unitTest("test@example.example.example.com", true, false, "", "100");
echo unitTest("test.example.com", false, false, "", "101");
echo unitTest("test.@example.com", false, false, "", "102");
echo unitTest("test..test@example.com", false, false, "", "103");
echo unitTest(".test@example.com", false, false, "", "104");
echo unitTest("test@test@example.com", false, false, "", "105");
echo unitTest("test@@example.com", false, false, "", "106");
echo unitTest("-- test --@example.com", false, false, "No spaces allowed in local part", "107");
echo unitTest("[test]@example.com", false, false, "Square brackets only allowed within quotes", "108");
echo unitTest("\"test\\test\"@example.com", true, true, "Any character can be escaped in a quoted string", "109");
echo unitTest("\"test\"test\"@example.com", false, false, "Quotes cannot be nested", "110");
echo unitTest("()[]\\;:,><@example.com", false, false, "Disallowed Characters", "111");
echo unitTest("test@.", false, false, "Dave Child says so", "112");
echo unitTest("test@example.", false, false, "Dave Child says so", "113");
echo unitTest("test@.org", false, false, "Dave Child says so", "114");
echo unitTest("test@123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012.com", false, false, "255 characters is maximum length for domain. This is 256.", "115");
echo unitTest("test@example", true, true, "Dave Child says so", "116");
echo unitTest("test@[123.123.123.123", false, false, "Dave Child says so", "117");
echo unitTest("test@123.123.123.123]", false, false, "Dave Child says so", "118");
echo unitTest("NotAnEmail", false, false, "Phil Haack says so", "119");
echo unitTest("@NotAnEmail", false, false, "Phil Haack says so", "120");
echo unitTest("\"test\\\\blah\"@example.com", true, true, "", "121");
echo unitTest("\"test\\blah\"@example.com", true, true, "Any character can be escaped in a quoted string", "122");
echo unitTest("\"test\\\rblah\"@example.com", true, true, "Quoted string specifically excludes carriage returns unless escaped", "123");
echo unitTest("\"test\rblah\"@example.com", false, false, "Quoted string specifically excludes carriage returns", "124");
echo unitTest("\"test\\\"blah\"@example.com", true, true, "", "125");
echo unitTest("\"test\"blah\"@example.com", false, false, "Phil Haack says so", "126");
echo unitTest("customer/department@example.com", true, false, "", "127");
echo unitTest("_Yosemite.Sam@example.com", true, false, "", "128");
echo unitTest("~@example.com", true, true, "", "129");
echo unitTest(".wooly@example.com", false, false, "Phil Haack says so", "130");
echo unitTest("wo..oly@example.com", false, false, "Phil Haack says so", "131");
echo unitTest("pootietang.@example.com", false, false, "Phil Haack says so", "132");
echo unitTest(".@example.com", false, false, "Phil Haack says so", "133");
echo unitTest("\"Austin@Powers\"@example.com", true, true, "", "134");
echo unitTest("Ima.Fool@example.com", true, false, "", "135");
echo unitTest("\"Ima.Fool\"@example.com", true, true, "", "136");
echo unitTest("\"Ima Fool\"@example.com", true, true, "", "137");
echo unitTest("Ima Fool@example.com", false, false, "Phil Haack says so", "138");
echo unitTest("phil.h\\@\\@ck@haacked.com", false, false, "Escaping can only happen in a quoted string", "139");
echo unitTest("\"first\".\"last\"@example.com", true, true, "", "140");
echo unitTest("\"first\".middle.\"last\"@example.com", true, true, "", "141");
echo unitTest("\"first\\\\\"last\"@example.com", false, false, "Contains an unescaped quote", "142");
echo unitTest("\"first\".last@example.com", true, true, "obs-local-part form as described in RFC 5322", "143");
echo unitTest("first.\"last\"@example.com", true, true, "obs-local-part form as described in RFC 5322", "144");
echo unitTest("\"first\".\"middle\".\"last\"@example.com", true, true, "obs-local-part form as described in RFC 5322", "145");
echo unitTest("\"first.middle\".\"last\"@example.com", true, true, "obs-local-part form as described in RFC 5322", "146");
echo unitTest("\"first.middle.last\"@example.com", true, true, "obs-local-part form as described in RFC 5322", "147");
echo unitTest("\"first..last\"@example.com", true, true, "obs-local-part form as described in RFC 5322", "148");
echo unitTest("foo@[\\1.2.3.4]", false, false, "RFC 5321 specifies the syntax for address-literal and does not allow escaping", "149");
echo unitTest("\"first\\\\\\\"last\"@example.com", true, true, "", "150");
echo unitTest("first.\"mid\\dle\".\"last\"@example.com", true, true, "Backslash can escape anything but must escape something", "151");
echo unitTest("Test.\r\n Folding.\r\n Whitespace@example.com", true, false, "", "152");
echo unitTest("first.\"\".last@example.com", false, false, "Contains a zero-length element", "153");
echo unitTest("first\\last@example.com", false, false, "Unquoted string must be an atom", "154");
echo unitTest("Abc\\@def@example.com", false, false, "Was incorrectly given as a valid address in the original RFC 3696", "155");
echo unitTest("Fred\\ Bloggs@example.com", false, false, "Was incorrectly given as a valid address in the original RFC 3696", "156");
echo unitTest("Joe.\\\\Blow@example.com", false, false, "Was incorrectly given as a valid address in the original RFC 3696", "157");
echo unitTest("first.last@[IPv6:1111:2222:3333:4444:5555:6666:12.34.567.89]", false, false, "IPv4 part contains an invalid octet", "158");
echo unitTest("\"test\\\r\n blah\"@example.com", false, false, "Folding white space can't appear within a quoted pair", "159");
echo unitTest("\"test\r\n blah\"@example.com", true, true, "This is a valid quoted string with folding white space", "160");
echo unitTest("{^c\\@**Dog^}@cartoon.com", false, false, "This is a throwaway example from Doug Lovell's article. Actually it's not a valid address.", "161");
echo unitTest("(foo)cal(bar)@(baz)iamcal.com(quux)", true, true, "A valid address containing comments", "162");
echo unitTest("cal@iamcal(woo).(yay)com", true, true, "A valid address containing comments", "163");
echo unitTest("\"foo\"(yay)@(hoopla)[1.2.3.4]", false, false, "Address literal can't be commented (RFC 5321)", "164");
echo unitTest("cal(woo(yay)hoopla)@iamcal.com", true, true, "A valid address containing comments", "165");
echo unitTest("cal(foo\\@bar)@iamcal.com", true, true, "A valid address containing comments", "166");
echo unitTest("cal(foo\\)bar)@iamcal.com", true, true, "A valid address containing comments and an escaped parenthesis", "167");
echo unitTest("cal(foo(bar)@iamcal.com", false, false, "Unclosed parenthesis in comment", "168");
echo unitTest("cal(foo)bar)@iamcal.com", false, false, "Too many closing parentheses", "169");
echo unitTest("cal(foo\\)@iamcal.com", false, false, "Backslash at end of comment has nothing to escape", "170");
echo unitTest("first().last@example.com", true, true, "A valid address containing an empty comment", "171");
echo unitTest("first.(\r\n middle\r\n )last@example.com", true, true, "Comment with folding white space", "172");
echo unitTest("first(12345678901234567890123456789012345678901234567890)last@(1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890)example.com", false, false, "Too long with comments, not too long without", "173");
echo unitTest("first(Welcome to\r\n the (\"wonderful\" (!)) world\r\n of email)@example.com", true, true, "Silly example from my blog post", "174");
echo unitTest("pete(his account)@silly.test(his host)", true, true, "Canonical example from RFC 5322", "175");
echo unitTest("c@(Chris's host.)public.example", true, true, "Canonical example from RFC 5322", "176");
echo unitTest("jdoe@machine(comment).  example", true, true, "Canonical example from RFC 5322", "177");
echo unitTest("1234   @   local(blah)  .machine .example", true, true, "Canonical example from RFC 5322", "178");
echo unitTest("first(middle)last@example.com", false, false, "Can't have a comment or white space except at an element boundary", "179");
echo unitTest("first(abc.def).last@example.com", true, true, "Comment can contain a dot", "180");
echo unitTest("first(a\"bc.def).last@example.com", true, true, "Comment can contain double quote", "181");
echo unitTest("first.(\")middle.last(\")@example.com", true, true, "Comment can contain a quote", "182");
echo unitTest("first(abc(\"def\".ghi).mno)middle(abc(\"def\".ghi).mno).last@(abc(\"def\".ghi).mno)example(abc(\"def\".ghi).mno).(abc(\"def\".ghi).mno)com(abc(\"def\".ghi).mno)", false, false, "Can't have comments or white space except at an element boundary", "183");
echo unitTest("first(abc\\(def)@example.com", true, true, "Comment can contain quoted-pair", "184");
echo unitTest("first.last@x(1234567890123456789012345678901234567890123456789012345678901234567890).com", true, true, "Label is longer than 63 octets, but not with comment removed", "185");
echo unitTest("a(a(b(c)d(e(f))g)h(i)j)@example.com", true, true, "", "186");
echo unitTest("a(a(b(c)d(e(f))g)(h(i)j)@example.com", false, false, "Braces are not properly matched", "187");
echo unitTest("name.lastname@domain.com", true, false, "", "188");
echo unitTest(".@", false, false, "", "189");
echo unitTest("a@b", true, true, "", "190");
echo unitTest("@bar.com", false, false, "", "191");
echo unitTest("@@bar.com", false, false, "", "192");
echo unitTest("a@bar.com", true, false, "", "193");
echo unitTest("aaa.com", false, false, "", "194");
echo unitTest("aaa@.com", false, false, "", "195");
echo unitTest("aaa@.123", false, false, "", "196");
echo unitTest("aaa@[123.123.123.123]", true, true, "", "197");
echo unitTest("aaa@[123.123.123.123]a", false, false, "extra data outside ip", "198");
echo unitTest("aaa@[123.123.123.333]", false, false, "not a valid IP", "199");
echo unitTest("a@bar.com.", false, false, "", "200");
echo unitTest("a@bar", true, true, "", "201");
echo unitTest("a-b@bar.com", true, false, "", "202");
echo unitTest("+@b.c", true, true, "TLDs can be any length", "203");
echo unitTest("+@b.com", true, true, "", "204");
echo unitTest("a@-b.com", false, false, "", "205");
echo unitTest("a@b-.com", false, false, "", "206");
echo unitTest("-@..com", false, false, "", "207");
echo unitTest("-@a..com", false, false, "", "208");
echo unitTest("a@b.co-foo.uk", true, false, "", "209");
echo unitTest("\"hello my name is\"@stutter.com", true, true, "", "210");
echo unitTest("\"Test \\\"Fail\\\" Ing\"@example.com", true, true, "", "211");
echo unitTest("valid@special.museum", true, false, "", "212");
echo unitTest("invalid@special.museum-", false, false, "", "213");
echo unitTest("shaitan@my-domain.thisisminekthx", true, false, "Disagree with Paul Gregg here", "214");
echo unitTest("test@...........com", false, false, "......", "215");
echo unitTest("foobar@192.168.0.1", true, true, "ip need to be []", "216");
echo unitTest("\"Joe\\\\Blow\"@example.com", true, true, "", "217");
echo unitTest("Invalid \\\n Folding \\\n Whitespace@example.com", false, false, "This isn't FWS so Dominic Sayers says it's invalid", "218");
echo unitTest("HM2Kinsists@(that comments are allowed)this.is.ok", true, true, "", "219");
echo unitTest("user%uucp!path@somehost.edu", true, false, "", "220");
echo unitTest("\"first(last)\"@example.com", true, true, "", "221");
echo unitTest(" \r\n (\r\n x \r\n ) \r\n first\r\n ( \r\n x\r\n ) \r\n .\r\n ( \r\n x) \r\n last \r\n (  x \r\n ) \r\n @example.com", true, true, "", "222");
echo unitTest("test. \r\n \r\n obs@syntax.com", true, true, "obs-fws allows multiple lines", "223");
echo unitTest("test. \r\n \r\n obs@syntax.com", true, true, "obs-fws allows multiple lines (test 2: space before break)", "224");
echo unitTest("test.\r\n\r\n obs@syntax.com", false, false, "obs-fws must have at least one WSP per line", "225");
echo unitTest("\"null \\ \"@char.com", true, true, "can have escaped null character", "226");
echo unitTest("\"null  \"@char.com", false, false, "cannot have unescaped null character", "227");
echo unitTest("null\\ @char.com", false, false, "escaped null must be in quoted string", "228");
echo unitTest("cdburgess+!#\$%&'*-/=?+_{}|~test@gmail.com", true, false, "Example given in comments", "229");
echo unitTest("first.last@[IPv6:::a2:a3:a4:b1:b2:b3:b4]", true, true, ":: only elides one zero group (IPv6 authority is RFC 4291)", "230");
echo unitTest("first.last@[IPv6:a1:a2:a3:a4:b1:b2:b3::]", true, true, ":: only elides one zero group (IPv6 authority is RFC 4291)", "231");
echo unitTest("first.last@[IPv6::]", false, false, "IPv6 authority is RFC 4291", "232");
echo unitTest("first.last@[IPv6:::]", true, true, "IPv6 authority is RFC 4291", "233");
echo unitTest("first.last@[IPv6::::]", false, true, "IPv6 authority is RFC 4291", "234");
echo unitTest("first.last@[IPv6::b4]", false, true, "IPv6 authority is RFC 4291", "235");
echo unitTest("first.last@[IPv6:::b4]", true, true, "IPv6 authority is RFC 4291", "236");
echo unitTest("first.last@[IPv6::::b4]", false, true, "IPv6 authority is RFC 4291", "237");
echo unitTest("first.last@[IPv6::b3:b4]", false, true, "IPv6 authority is RFC 4291", "238");
echo unitTest("first.last@[IPv6:::b3:b4]", true, true, "IPv6 authority is RFC 4291", "239");
echo unitTest("first.last@[IPv6::::b3:b4]", false, true, "IPv6 authority is RFC 4291", "240");
echo unitTest("first.last@[IPv6:a1::b4]", true, true, "IPv6 authority is RFC 4291", "241");
echo unitTest("first.last@[IPv6:a1:::b4]", false, true, "IPv6 authority is RFC 4291", "242");
echo unitTest("first.last@[IPv6:a1:]", false, true, "IPv6 authority is RFC 4291", "243");
echo unitTest("first.last@[IPv6:a1::]", true, true, "IPv6 authority is RFC 4291", "244");
echo unitTest("first.last@[IPv6:a1:::]", false, true, "IPv6 authority is RFC 4291", "245");
echo unitTest("first.last@[IPv6:a1:a2:]", false, true, "IPv6 authority is RFC 4291", "246");
echo unitTest("first.last@[IPv6:a1:a2::]", true, true, "IPv6 authority is RFC 4291", "247");
echo unitTest("first.last@[IPv6:a1:a2:::]", false, true, "IPv6 authority is RFC 4291", "248");
echo unitTest("first.last@[IPv6:0123:4567:89ab:cdef::]", true, true, "IPv6 authority is RFC 4291", "249");
echo unitTest("first.last@[IPv6:0123:4567:89ab:CDEF::]", true, true, "IPv6 authority is RFC 4291", "250");
echo unitTest("first.last@[IPv6:::a3:a4:b1:ffff:11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "251");
echo unitTest("first.last@[IPv6:::a2:a3:a4:b1:ffff:11.22.33.44]", true, true, ":: only elides one zero group (IPv6 authority is RFC 4291)", "252");
echo unitTest("first.last@[IPv6:a1:a2:a3:a4::11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "253");
echo unitTest("first.last@[IPv6:a1:a2:a3:a4:b1::11.22.33.44]", true, true, ":: only elides one zero group (IPv6 authority is RFC 4291)", "254");
echo unitTest("first.last@[IPv6::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "255");
echo unitTest("first.last@[IPv6::::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "256");
echo unitTest("first.last@[IPv6:a1:11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "257");
echo unitTest("first.last@[IPv6:a1::11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "258");
echo unitTest("first.last@[IPv6:a1:::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "259");
echo unitTest("first.last@[IPv6:a1:a2::11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "260");
echo unitTest("first.last@[IPv6:a1:a2:::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "261");
echo unitTest("first.last@[IPv6:0123:4567:89ab:cdef::11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "262");
echo unitTest("first.last@[IPv6:0123:4567:89ab:cdef::11.22.33.xx]", false, true, "IPv6 authority is RFC 4291", "263");
echo unitTest("first.last@[IPv6:0123:4567:89ab:CDEF::11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "264");
echo unitTest("first.last@[IPv6:0123:4567:89ab:CDEFF::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "265");
echo unitTest("first.last@[IPv6:a1::a4:b1::b4:11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "266");
echo unitTest("first.last@[IPv6:a1::11.22.33]", false, true, "IPv6 authority is RFC 4291", "267");
echo unitTest("first.last@[IPv6:a1::11.22.33.44.55]", false, true, "IPv6 authority is RFC 4291", "268");
echo unitTest("first.last@[IPv6:a1::b211.22.33.44]", false, true, "IPv6 authority is RFC 4291", "269");
echo unitTest("first.last@[IPv6:a1::b2:11.22.33.44]", true, true, "IPv6 authority is RFC 4291", "270");
echo unitTest("first.last@[IPv6:a1::b2::11.22.33.44]", false, true, "IPv6 authority is RFC 4291", "271");
echo unitTest("first.last@[IPv6:a1::b3:]", false, true, "IPv6 authority is RFC 4291", "272");
echo unitTest("first.last@[IPv6::a2::b4]", false, true, "IPv6 authority is RFC 4291", "273");
echo unitTest("first.last@[IPv6:a1:a2:a3:a4:b1:b2:b3:]", false, true, "IPv6 authority is RFC 4291", "274");
echo unitTest("first.last@[IPv6::a2:a3:a4:b1:b2:b3:b4]", false, true, "IPv6 authority is RFC 4291", "275");
?>
</body>

</html>