--TEST--
Verifies return not-string should raise a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectStdClass(new \StdClass);

echo "OK1\n";

$object->expectStdClass(new SplStack());

echo "OK2\n";
?>
--EXPECTF--
OK1

%Aatal error: Argument 1 passed to StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::{closure}() must be an instance of stdClass, instance of SplStack given%a
