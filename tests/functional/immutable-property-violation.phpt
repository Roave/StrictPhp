--TEST--
Verifies that writing a non-object to an object-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithImmutableProperty();

$object->property = 'stuff';

echo 'OK';

$object->property = 'overwrite (not possible)'
?>
--EXPECT--
OK
PHP Catchable fatal error:%a