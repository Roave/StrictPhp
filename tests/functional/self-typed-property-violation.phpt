--TEST--
Verifies that writing a non-object to an object-typed property causes a fatal error
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

%APHP Catchable fatal error:%a
