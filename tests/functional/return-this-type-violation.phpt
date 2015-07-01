--TEST--
Verifies return not-self compatible should raises a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectThis($object);

echo "OK1\n";

$object->expectThis(new DateTime());

echo "OK2\n";
?>
--EXPECTF--
OK1

%Aatal error: Argument 1 passed to %aObjectTypeChecker%a must be an instance of StrictPhpTestAsset\ClassWithReturnTypeMethod%a instance of DateTime given%a
