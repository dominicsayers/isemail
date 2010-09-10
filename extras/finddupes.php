<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<title>Finding any duplicate tests or IDs</title>
	</head>

	<body>
<?php
require_once '../is_email.php';

$addresses		= array();
$ids			= array();

$duplicates		= array();
$duplicates['address']	= array();
$duplicates['id']	= array();

$dirty 			= false;

$document		= new DOMDocument();

$document->load('../test/tests.xml');

$tests		= $document->getElementsByTagName('tests')->item(0);
$version	= $tests->getAttribute("version");
$testList	= $document->getElementsByTagName('test');

for ($i = 0; $i < $testList->length; ++$i) {
	$test		= $testList->item($i);
	$tagList	= $test->childNodes;

	unset($id);

	for ($j = 0; $j < $tagList->length; $j++) {
		$node = $tagList->item($j);
		if ($node->nodeType === XML_ELEMENT_NODE) {
			$name	= $node->nodeName;
			$$name	= $node->nodeValue;
		}
	}

	if (in_array($address, $addresses)) {
		$duplicates['address'][]	= $address;
		$duplicates['addressIDs'][]	= $id;
	} else {
		$addresses[]			= $address;

		//	Add ID if it hasn't got one
		if (!isset($id)) {
			$dirty = true;
			$test->appendChild($document->createElement((string) 'id', $i + 1));
			$test->appendChild($document->createTextNode("\n")); // Just for pretty
		}
	}

	if (in_array($id, $ids)) {
		$duplicates['id'][]	= $id;
	} else {
		$ids[]			= $id;
	}
}

if ($dirty) $document->save('new_tests.xml');

//-$count = $results['count'];

echo "<p><strong>Duplicate addresses</strong></p>\n";

foreach ($duplicates['address'] as $address) {
	echo "<p>$address</p>\n";
}

echo "<br /><p><strong>Duplicate IDs</strong></p>\n";

foreach ($duplicates['id'] as $id) {
	echo "<p>$id</p>\n";
}

?>
</body>
</html>
