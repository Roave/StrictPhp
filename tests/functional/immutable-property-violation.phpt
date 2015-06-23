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
--EXPECTF--
OK
%AFatal error: Uncaught exception 'RuntimeException' with message 'Trying to overwrite property %a#$property of object %a#%a with a value of type "string". The property was already given a value of type string%a