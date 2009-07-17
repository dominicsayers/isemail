<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>is_email() - Build unit test script from test data</title>
</head>

<body>
<?php
// Top of PHP script
$php = <<<PHP
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
require_once '..\is_email.php';

function unitTest (\$email, \$expected, \$comment = '') {
	\$valid		= is_email(\$email);
	\$not		= (\$valid) ? 'Valid' : 'Not valid';
	\$unexpected	= (\$valid !== \$expected) ? " <b>\$not</b>" : "\$not";
	\$comment		= (\$comment === '') ? "&nbsp;" : stripslashes("\$comment");
	
	return "<div><p class=\\"address\\"<em>\$email</em></p><p class=\\"valid\\">\$unexpected</p><p class=\\"comment\\">\$comment</p></div>\n";
}


PHP;

$document = new DOMDocument();
$document->load('tests.xml');

// Get version
$suite = $document->getElementsByTagName('tests')->item(0);

if ($suite->hasAttribute('version')) {
	$version = $suite->getAttribute('version');
	$php .= "echo \"<h3>Email address validation test suite version $version</h3>\\n\";\n";
}

$php .= "echo \"<p class=\\\"author\\\">Dominic Sayers | <a href=\\\"mailto:dominic_sayers@hotmail.com\\\">dominic_sayers@hotmail.com</a> | <a href=\\\"http://www.dominicsayers.com/isemail\\\">RFC-compliant email address validation</a></p>\\n<br>\\n<hr>\\n\";\n";

$testList = $document->getElementsByTagName('test');

for ($i = 0; $i < $testList->length; $i++) {
	$tagList = $testList->item($i)->childNodes;

	$address	= '';
	$valid		= '';
	$comment	= '';

	for ($j = 0; $j < $tagList->length; $j++) {
		$node = $tagList->item($j);
		if ($node->nodeType === XML_ELEMENT_NODE) {
			$name	= $node->nodeName;
			$$name	= $node->nodeValue;
		}
	}

	$expected	= ($valid === 'true') ? true : false;
	$address	= str_replace('\\0', chr(0), $address);	// Can't use &#00; in XML file
	$address	= addslashes($address);
	$address	= str_replace(array(chr(9),chr(10),chr(13)), array('\t','\n','\r'), $address);
	$address	= str_replace('$', '\\$', $address);
	$comment	= addslashes($comment);
	$comment	= str_replace('$', '\\$', $comment);

	$php .= "echo unitTest(\"$address\", $valid, \"$comment\");\n";
}

// Bottom of PHP script
$php .= '?';
$php .= <<<PHP
>
</body>

</html>
PHP;

$handle = @fopen('tests.php', 'wb');
if ($handle === false) die("Can't open tests.php for writing");
fwrite($handle, $php);
fclose($handle);

?>
<p>Successfully created tests.php</p>
<p>Click <a href="tests.php">here</a> to run the tests.</p>
</body>

</html>
