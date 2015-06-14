--TEST--
Verifies that writing a non-object to an object-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithTypedArrayProperty();

$object->property = [];

echo "OK1\n";

$object->property = new ArrayObject([]);

echo "OK2\n";

$object->property = [new stdClass(), new stdClass()];

echo "OK3\n";

$object->property = new ArrayObject([new stdClass(), new stdClass()]);

echo "OK4\n";

$object->property = [new stdClass(), new \StrictPhpTestAsset\ClassWithTypedArrayProperty()];
?>
--EXPECT--
OK1
OK2
OK3
OK4
PHP Catchable fatal error:%a