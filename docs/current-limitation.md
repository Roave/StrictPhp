---
currentMenu: current-limitation
---

## Current limitations

This package uses [voodoo magic](http://ocramius.github.io/voodoo-php/) to
operate, specifically [go-aop-php](https://github.com/lisachenko/go-aop-php).

Go AOP PHP has some limitations when it comes to intercepting access to
private class members, so please be aware that it has limited scope (for now).

This package only works against autoloaded classes; classes that aren't handled by
an autoloader cannot be rectified by StrictPhp.
