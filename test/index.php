<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="chrome=1"/>
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
		p.navigation	{font-size:11px;}
		p.statistics	{width:100%;color:white;font-weight:bold;text-align:center;padding:0.25em;}
		p.green		{background-color:green;}
		p.amber		{background-color:#FF9900;color:black;}
		p.red		{background-color:red;color:#FFFF66;}
		hr		{clear:left;}
	</style>
</head>

<body>
	<h2 id="top">RFC-compliant email address validation</h2>
	<p>
		<a href="?all" >Run all tests</a>			<!-- Revision 2.11: Now a link instead of a button on a form -->
		| <a href="?set=tests-beta.xml" >Run beta test set</a>	<!-- Revision 2.11: evaluating Michael Rushton's new test set -->
		| <a href="mailto:dominic@sayers.cc?subject=is_email()">Dominic Sayers</a>
		| <a href="http://www.dominicsayers.com/isemail" target="_blank">Read more...</a>
	</p>
	<hr/>
<?php
// Incorporates formatting suggestions from Daniel Marschall (uni@danielmarschall.de)
require_once '../is_email.php';
require_once '../extras/is_email_statustext.php';

/*.array[string]mixed.*/ function unitTest ($email, $valid_expected = true, /*.mixed.*/ $warn_type = 0) {
	$result			= /*.(array[string]mixed).*/ array();
	$diagnosis		= is_email($email, true, E_WARNING);	// revision 2.5: Pass E_WARNING (as intended)

	$result['diagnosis']	= $diagnosis;
	$result['text']		= is_email_statustext($diagnosis, ISEMAIL_STATUSTEXT_EXPLANATORY);
	$result['constant']	= is_email_statustext($diagnosis, ISEMAIL_STATUSTEXT_CONSTANT);
	$result['smtpcode']	= is_email_statustext($diagnosis, ISEMAIL_STATUSTEXT_SMTPCODE);

	$result['warn']		= (($diagnosis & ISEMAIL_WARNING) !== 0);
	$result['valid']	= ($diagnosis < ISEMAIL_ERROR);

	$warn_expected		= (is_bool($warn_type))	? $warn_type : $result['warn'];	// Revision 2.11: We don't care, unless the expectation was explicitly set

	$result['alert_warn']	= ($result['warn']	!== $warn_expected);
	$result['alert_valid']	= ($result['valid']	!== $valid_expected);

	return $result;
}

/*.string.*/ function all_tests($test_set = 'tests.xml') {
	$document = new DOMDocument();
	$document->load($test_set);

	// Get version
	$suite = $document->getElementsByTagName('tests')->item(0);

	if ($suite->hasAttribute('version')) {
		$version = $suite->getAttribute('version');
		echo "\t<h3>Test package version $version</h3>\r\n";
	}

	$nodeList		= $document->getElementsByTagName('description');
	$description		= ($nodeList->length === 0) ? '' : "\t" . '<p class="navigation">' . $document->saveXML($nodeList->item(0)) . '</p>' . PHP_EOL;

	echo <<<PHP
$description	<p class="navigation">This output is very wide - you should probably maximize your browser window. <a href="#bottom">Go to bottom of page &raquo;</a></p>
	<br/>
	<div class="heading address">Address</div>
	<div class="heading id">#</div>
	<div class="heading valid">Result</div>
	<div class="heading warning">Warning</div>
	<div class="heading diagnosis">Diagnosis</div>
	<div class="heading comment">Comment</div>
	<br/>

PHP;

	$testList		= $document->getElementsByTagName('test');
	$testCount		= $testList->length;
	$statistics_count	= 0;
	$statistics_alert_warn	= 0;
	$statistics_alert_valid	= 0;
	$html			= '';

	for ($i = 0; $i < $testCount; $i++) {
		$tagList = $testList->item($i)->childNodes;

		$address	= '';
		$valid		= 'false';
		$warning	= '';
		$comment	= '';
		$id		= '';

		for ($j = 0; $j < $tagList->length; $j++) {
			$node = $tagList->item($j);
			if ($node->nodeType === XML_ELEMENT_NODE) {
				$name	= $node->nodeName;
				$$name	= $node->nodeValue;
			}
		}

		// Can't store ASCII NUL or Unicode Character 'NULL' (U+0000) in XML file so we put a token in the XML
		// The token we have chosen is the Unicode Character 'SYMBOL FOR NULL' (U+2400)
		// Here we convert the token to an ASCII NUL.
		$needles	= array(mb_convert_encoding('&#9216;', 'UTF-8', 'HTML-ENTITIES'));	// PHP bug doesn't allow us to use hex notation (http://bugs.php.net/48645)
		$substitutes	= array(chr(0));
		$email		= str_replace($needles, $substitutes, $address);
		$comment	= str_replace($needles, $substitutes, $comment);
		$warn_type	= ($warning === '') ? 0 : ($warning === 'true');	// Revision 2.11: Warning expectation can be true, false or non-existent

		$result		= unitTest($email, ($valid === 'true'), $warn_type);

		$valid		= $result['valid'];
		$warn		= $result['warn'];
		$constant	= $result['constant'];
		$text		= $result['text'];

		$warning	= ($warn)			? 'Yes'		: 'No';
		$status		= ($valid)			? 'Valid'	: 'Not valid';
		$class_warn	= ($result['alert_warn'])	? ' unexpected'	: '';
		$class_valid	= ($result['alert_valid'])	? ' unexpected'	: '';

		$comment	= stripslashes($comment);

		if ($text !== '')	$comment	.= ($comment === '') ? stripslashes($text) : ' (' . stripslashes($text) . ')';
		if ($comment === '')	$comment	= "&nbsp;";
		if ($email === '')	$email		= "&nbsp;";

		echo <<<HTML
	<div class="address"><em>$email</em></div>
	<div class="id">$id</div>
	<div class="valid$class_valid">$status</div>
	<div class="warning$class_warn">$warning</div>
	<div class="diagnosis">$constant</div>
	<div class="comment">$comment</div>
	<br/>

HTML;

		// Update statistics for this test
		$statistics_count++;
		$statistics_alert_warn	+= ($result['alert_warn'])	? 1 : 0;
		$statistics_alert_valid	+= ($result['alert_valid'])	? 1 : 0;
	}

	// Revision 2.7: Added test run statistics
	if	($statistics_alert_valid	!== 0)	$statistics_class = 'red';
	else if	($statistics_alert_warn		!== 0)	$statistics_class = 'amber';
	else						$statistics_class = 'green';

	$statistics_plural_count	= ($statistics_count		=== 1)	? '' : 's';
	$statistics_plural_valid	= ($statistics_alert_valid	=== 1)	? '' : 's';
	$statistics_plural_warn		= ($statistics_alert_warn	=== 1)	? '' : 's';

	echo <<<PHP
	<p class="statistics $statistics_class">$statistics_count test$statistics_plural_count: $statistics_alert_valid unexpected result$statistics_plural_valid, $statistics_alert_warn unexpected warning$statistics_plural_warn</p>
	<hr/>
	<p class="navigation"><a id="bottom" href="#top">&laquo; back to top</a></p>
PHP;
}

/*.string.*/ function test_single_address(/*.string.*/ $email) {
	$result		= unitTest($email);

	$valid		= $result['valid'];
	$warn		= $result['warn'];
	$constant	= $result['constant'];
	$diagnosis_text	= $result['text'];
	$smtpcode	= $result['smtpcode'];

	$result_text	= ($valid) ? (($warn) ? 'valid but a warning was raised' : 'valid and no warnings were raised') : '<strong>invalid</strong>';
	$commentary	= (!$valid || $warn) ? "<br/>The diagnostic code was $constant ($diagnosis_text)" : '';

	echo <<<HTML
	<p>Email address tested was <em>$email</em></p>
	<p>The address is $result_text$commentary</p>
	<p>The SMTP enhanced status code is <em>$smtpcode</em></p>
	<hr/>
HTML;
}

/*.string.*/ function forms_html(/*.string.*/ $email = '') {
	$value = ($email === '') ? '' : ' value="' . htmlspecialchars($email) . '"';

	return <<<PHP
	<form>
		<label for="address">Test this email address:</label>
		<input type="text"$value name="address"/>
		<input type="submit" value="Test"/>
	</form>
	<hr/>

PHP;
}

if (isset($_GET) && is_array($_GET)) {
	if (array_key_exists('address', $_GET)) {
		$email = $_GET['address'];
		if (get_magic_quotes_gpc() !== 0) $email = stripslashes($email); // Version 2.6: BUG: The online test page didn't take account of the magic_quotes_gpc setting that some hosting providers insist on setting. Including mine.
		echo forms_html($email);
		test_single_address($email);
	} else if (array_key_exists('all', $_GET)) {
		echo forms_html();
		all_tests();
	} else if (array_key_exists('set', $_GET)) {	// Revision 2.11: Run any arbitrary test set
		echo forms_html();
		all_tests($_GET['set']);
	} else {
		echo forms_html();
	}
}
?>

</body>
</html>
