--TEST--
Verifies that writing a non self compatible object to an type hinted property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithSameTypedProperty();

$object->property = new \StrictPhpTestAsset\ClassWithSameTypedProperty();

echo "OK\n";

$object->property = new stdClass();

echo 'Never reached!';
?>
--EXPECTF--
OK

%ACatchable fatal error: Argument 1 passed to %a must be an instance of StrictPhpTestAsset\ClassWithSameTypedProperty, instance of stdClass given%a
