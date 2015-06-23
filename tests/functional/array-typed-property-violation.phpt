--TEST--
Verifies that writing a non-array to an array-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithGenericArrayTypedProperty();

$object->property = 'non-array';
?>
--EXPECTF--
%ACatchable fatal error: Argument 1 passed to %a must be of the type array, string given%a
