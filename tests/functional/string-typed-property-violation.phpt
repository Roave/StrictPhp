--TEST--
Verifies that writing a non-string to an string-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithGenericStringTypedProperty();

$object->property = 123;
?>
--EXPECTF--
%AFatal error: Uncaught exception 'ErrorException' with message 'NOPE'%a
