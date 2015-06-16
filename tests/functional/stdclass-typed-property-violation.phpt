--TEST--
Verifies that writing a non-object to an object-typed property causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

$object = new \StrictPhpTestAsset\ClassWithStdClassTypedProperty();

$object->property = new \StrictPhpTestAsset\ClassWithStdClassTypedProperty();
?>
--EXPECT--
PHP Catchable fatal error:%a