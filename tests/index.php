<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>devpkg() - Build unit test script from test data</title>
</head>

<body>
<?php
// Top of PHP script
$php = <<<PHP
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

function unitTest (\$email, \$expected, \$warn_expected, \$comment = '', \$id = '') {
	\$diagnosis	= devpkg(\$email, false, true);
	\$text		= is_email_statustext(\$diagnosis);

	\$warn		= ((\$diagnosis & ISEMAIL_WARNING) !== 0);
	\$valid		= (\$diagnosis < ISEMAIL_ERROR);

	\$warning	= (\$warn) ? \$diagnosis : '&nbsp;';
	\$result		= (\$valid) ? 'Valid' : 'Not valid';

	if (\$valid	!== \$expected)		\$result		= "<strong>\$result</strong>";
	if (\$warn	!== \$warn_expected)	\$warning	= "<strong>\$warning</strong>";

	\$comment	= stripslashes(\$comment);

	if (\$text !== '')	\$comment .= (\$comment === '') ? stripslashes(\$text) : ' (' . stripslashes(\$text) . ')';
	if (\$comment === '')	\$comment = "&nbsp;";

	return "<div><p class=\\"address\\"<em>\$email</em></p><p class=\\"id\\">\$id</p><p class=\\"valid\\">\$result</p><p class=\\"warning\\">\$warning</p><p class=\\"diagnosis\\">\$diagnosis</p><p class=\\"comment\\">\$comment</p></div>\\n";
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

$php .= <<<PHP
echo "<p class=\\"author\\">Dominic Sayers | <a href=\\"mailto:dominic@sayers.cc\\">dominic@sayers.cc</a> | <a href=\\"http://www.dominicsayers.com/isemail\\">RFC-compliant email address validation</a></p>\\n<br>\\n<hr>\\n";
echo "<div><p class=\\"address\\"<strong>Address</strong></p><p class=\\"id\\"><strong>Test #</strong></p><p class=\\"valid\\"><strong>Result</strong></p><p class=\\"warning\\"><strong>Warning</strong></p><p class=\\"diagnosis\\"><strong>Diagnosis</strong></p><p class=\\"comment\\"><strong>Comment</strong></p></div>\\n";
PHP;

$testList = $document->getElementsByTagName('test');

for ($i = 0; $i < $testList->length; $i++) {
	$tagList = $testList->item($i)->childNodes;

	$address	= '';
	$valid		= 'false';
	$warning	= 'false';
	$comment	= '';

	for ($j = 0; $j < $tagList->length; $j++) {
		$node = $tagList->item($j);
		if ($node->nodeType === XML_ELEMENT_NODE) {
			$name	= $node->nodeName;
			$$name	= $node->nodeValue;
		}
	}

//-	$expected	= ($valid === 'true') ? true : false;
	$needles	= array('\\0'	, '\\'		, '"'	, '$'	, chr(9)	,chr(10)	,chr(13));
	$substitutes	= array(chr(0)	, '\\\\'	, '\\"'	, '\\$'	, '\t'		,'\n'		,'\r');
	$address	= str_replace($needles, $substitutes, $address);
	$comment	= str_replace($needles, $substitutes, $comment);

	$php .= "echo unitTest(\"$address\", $valid, $warning, \"$comment\", \"$id\");\n";
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
