--TEST--
Verifies return not-object should raise a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectObject(new \StdClass);

echo "OK1\n";

$object->expectObject(new \DateTime());

echo "OK2\n";

$object->expectObject('non-object');

echo "OK3\n";

?>
--EXPECTF--
OK1
OK2

%AFatal error: Uncaught exception 'ErrorException' with message 'NOPE'%a
