--TEST--
Verifies return not-static compatible should raise a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectStatic($object);

echo "OK1\n";

$object->expectStatic(new DateTime());

echo "OK2\n";
?>
--EXPECTF--
OK1

%Aatal error: Argument 1 passed to %aObjectTypeChecker::{closure}() must be an instance of %aClassWithReturnTypeMethod%a instance of DateTime given%a
