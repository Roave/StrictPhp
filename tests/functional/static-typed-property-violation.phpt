--TEST--
Verifies that writing a non-object to an object-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithStaticTypedProperty();

$object->property = new \StrictPhpTestAsset\ClassWithStaticTypedProperty();

echo "OK\n";

$object->property = new stdClass();

echo 'Never reached!';
?>
--EXPECTF--
OK

PHP Catchable fatal error:%a
