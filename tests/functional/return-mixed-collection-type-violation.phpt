--TEST--
Verifies return not-boolean or string collection should raise a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithReturnTypeMethod();

$object->expectMixedDataCollection([['yada', 'yada']]);

echo "OK1\n";

$object->expectMixedDataCollection([true, false]);

echo "OK2\n";

$object->expectMixedDataCollection([[true], ['string'], true]);

echo "OK3\n";
?>
--EXPECTF--
OK1
OK2

%AFatal error: Uncaught exception 'ErrorException' with message 'NOPE'%a
