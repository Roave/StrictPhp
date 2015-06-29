--TEST--
Verifies that calling a method with a typed array parameter annotation causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

(new \StrictPhpTestAsset\ClassWithTypedArrayMethodParameterAnnotation())
    ->method([]);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithTypedArrayMethodParameterAnnotation())
    ->method([1, 2, 3]);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithTypedArrayMethodParameterAnnotation())
    ->method([new stdClass(), new stdClass(), new stdClass()]);

echo "OK\n";

(new \StrictPhpTestAsset\ClassWithTypedArrayMethodParameterAnnotation())
    ->method([new \StrictPhpTestAsset\ClassWithTypedArrayMethodParameterAnnotation()]);

echo "Never reached\n";

?>
--EXPECTF--
OK
OK
OK
%Aatal error: Argument 1 passed to %s must be an instance of stdClass, instance of %a given%a