--TEST--
Verifies that writing a wrong object type to an strict object typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithStdClassTypedProperty();

$object->property = new \StrictPhpTestAsset\ClassWithStdClassTypedProperty();
?>
--EXPECTF--
%ACatchable fatal error: Argument 1 passed to%a must be an instance of stdClass, instance of StrictPhpTestAsset\ClassWithStdClassTypedProperty given%a
