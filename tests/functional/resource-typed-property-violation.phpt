--TEST--
Verifies that writing a non-resource to an resource-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithGenericResourceTypedProperty();

$object->property = 123;
?>
--EXPECTF--
%AFatal error: Uncaught exception 'ErrorException' with message 'Unsupported type "object" given, expecting "resource"'%a
