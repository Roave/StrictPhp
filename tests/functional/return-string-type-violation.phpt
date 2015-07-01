--TEST--
Verifies return types to be correctly given, if not a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectString('yada yada');

echo "OK1\n";

$object->expectString(1e4);

echo "OK2\n";
?>
--EXPECTF--
OK1

%AFatal error: Uncaught exception 'ErrorException' with message 'NOPE'%a
