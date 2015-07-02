--TEST--
Verifies that calling a method with a typed multiple array parameter annotation causes a fatal error
--FILE--
<?php

require_once __DIR__ . '/init.php';

(new \StrictPhpTestAsset\ClassHintingAgainstImportOfOwnNamespace)
    ->method(new StrictPhpTestAsset\ClassThatDependsOnHello);

echo "OK\n";

?>
--EXPECTF--
OK