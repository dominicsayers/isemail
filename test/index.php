<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Testing is_email()</title>


	<style type="text/css">
		body		{font-family:Segoe UI,Arial,Helvetica,sans-serif;}
		br		{clear:left;}
		div		{margin:0;padding:0;float:left;font-size:12px;}
		div.valid	{width:75px;}
		div.warning	{width:50px;}
		div.diagnosis	{width:220px;}
		div.unexpected, div.heading
				{font-weight:bold;}
		div.address	{text-align:right;width:400px;overflow:hidden;margin-right:8px;}
		div.id		{text-align:right;width:20px;overflow:hidden;margin-right:8px;}
		div.author	{font-style:italic;}
		hr		{clear:left;}
	</style>
</head>

<body>
<?php
// Incorporates formatting suggestions from Daniel Marschall (uni@danielmarschall.de)
require_once '../is_email.php';
require_once '../extras/is_email_statustext.php';

/*.array[string]mixed.*/ function unitTest ($address, $valid_expected = true, $warn_expected = false) {
	$result			= /*.(array[string]mixed).*/ array();
	$diagnosis		= is_email($address, true, true);

	$result['diagnosis']	= $diagnosis;
	$result['text']		= is_email_statustext($diagnosis);
	$result['constant']	= is_email_statustext($diagnosis, false);

	$result['warn']		= (($diagnosis & ISEMAIL_WARNING) !== 0);
	$result['valid']	= ($diagnosis < ISEMAIL_ERROR);

	$result['alert_warn']	= ($result['warn']	!== $warn_expected);
	$result['alert_valid']	= ($result['valid']	!== $valid_expected);

	return $result;
}

/*.string.*/ function all_tests() {
	$document = new DOMDocument();
	$document->load('tests.xml');

	// Get version
	$suite = $document->getElementsByTagName('tests')->item(0);

	if ($suite->hasAttribute('version')) {
		$version = $suite->getAttribute('version');
		echo "\t<h3>Email address validation test suite version $version</h3>\r\n";
	}

	echo <<<PHP
	<p class="author">Dominic Sayers | <a href="mailto:dominic@sayers.cc">dominic@sayers.cc</a> | <a href="http://www.dominicsayers.com/isemail">RFC-compliant email address validation</a></p>
	<hr />
	<div class="heading address">Address</div>
	<div class="heading id">#</div>
	<div class="heading valid">Result</div>
	<div class="heading warning">Warning</div>
	<div class="heading diagnosis">Diagnosis</div>
	<div class="heading comment">Comment</div>
	<br />

PHP;

	$testList	= $document->getElementsByTagName('test');
	$html		= '';

	for ($i = 0; $i < $testList->length; $i++) {
		$tagList = $testList->item($i)->childNodes;

		$address	= '';
		$valid		= 'false';
		$warning	= 'false';
		$comment	= '';
		$id		= '';

		for ($j = 0; $j < $tagList->length; $j++) {
			$node = $tagList->item($j);
			if ($node->nodeType === XML_ELEMENT_NODE) {
				$name	= $node->nodeName;
				$$name	= $node->nodeValue;
			}
		}

		// Can't store Unicode Character 'NULL' (U+0000) in XML file so replace our token(s) with genuine NULs
		$needles	= array('␀');
		$substitutes	= array(chr(0));
		$address	= str_replace($needles, $substitutes, $address);
		$comment	= str_replace($needles, $substitutes, $comment);

		$result		= unitTest($address, ($valid === 'true'), ($warning === 'true'));

		$valid		= $result['valid'];
		$warn		= $result['warn'];
		$constant	= $result['constant'];
		$text		= $result['text'];

		$warning	= ($warn)			? 'Yes'		: 'No';
		$status		= ($valid)			? 'Valid'	: 'Not valid';
		$class_warn	= ($result['alert_warn'])	? ' unexpected'	: '';
		$class_valid	= ($result['alert_valid'])	? ' unexpected'	: '';

		$comment	= stripslashes($comment);

		if ($text !== '')	$comment .= ($comment === '') ? stripslashes($text) : ' (' . stripslashes($text) . ')';
		if ($comment === '')	$comment = "&nbsp;";

		echo <<<HTML
	<div class="address"><em>$address</em></div>
	<div class="id">$id</div>
	<div class="valid$class_valid">$status</div>
	<div class="warning$class_warn">$warning</div>
	<div class="diagnosis">$constant</div>
	<div class="comment">$comment</div>
	<br />

HTML;
	}

	echo "\t<hr />\r\n";
}

/*.string.*/ function test_single_address(/*.string.*/ $address) {
	$result		= unitTest($address);

	$valid		= $result['valid'];
	$warn		= $result['warn'];
	$constant	= $result['constant'];
	$diagnosis_text	= $result['text'];

	$result_text	= ($valid) ? (($warn) ? 'valid but a warning was raised' : 'valid and no warnings were raised') : '<strong>invalid</strong>';
	$commentary	= (!$valid || $warn) ? "<br />The diagnostic code was $constant ($diagnosis_text)" : '';

	echo <<<HTML
	<p>Email address tested was <em>$address</em></p>
	<p>The address is $result_text$commentary</p>
	<hr />
HTML;
}

if (isset($_GET) && is_array($_GET)) {
	if	(array_key_exists('address', $_GET))		test_single_address($_GET['address']);
	else if	(array_key_exists('all', $_GET))	all_tests();
}
?>

	<form>
		<input type="hidden" name="all" />
		<input type="submit" value="Run all tests" />
	</form>
	<br />
	<form>
		<label for="address">Test this email address:</label>
		<input type="text" name="address" />
		<input type="submit" value="Test" />
	</form>
</body>
</html>
