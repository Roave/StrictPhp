--TEST--
Verifies that writing a non self type object to an type hinted property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithSelfTypedProperty();

$object->property = new \StrictPhpTestAsset\ClassWithSelfTypedProperty();

echo "OK\n";

$object->property = new stdClass();

echo 'Never reached!';
?>
--EXPECTF--
OK

%ACatchable fatal error:%amust be an instance of %a, instance of stdClass given,%a
