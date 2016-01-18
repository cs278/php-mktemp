PHP mktemp
==========

Create temporary files and directories in PHP, safely.

Sadly PHP is deficient when it comes to creating temporary files and directories
in a safe and secure manner. This library aims to address these short-comings.

Usage
-----

* `Cs278\Mktemp\file(string|null $template, string|null $dir)`

  Create a temporary file with a supplied template for the name.

  The template should be a string containing a sequence of 3 consecutive `X`
  characters, these characters will be replaced with a random component. If this
  argument is `null` a default `tmp.XXXXXX` template will be used.

  The directory should be a path that exists and the user can write to, if this
  argument is not supplied the default temporary directory will be used.

  A `\RuntimeException` will be thrown if a file cannot be created.

  ```php
  use Cs278\Mktemp\file;

  var_dump(file());
  // /tmp/tmp.P9aLnd
  var_dump(file('output.XXXX.pdf'));
  // /tmp/output.oI7b.pdf
  var_dump(file(null, '/var/tmp'));
  // /var/tmp/tmp.8uJx
  var_dump(file('test.XXX.html', '/var/tmp'));
  // /var/tmp/test.9h2.html
  ```

* `Cs278\Mktemp\dir(string|null $template, string|null $dir)`

  Create a temporary directory with a supplied template for the name.

  The template should be a string containing a sequence of 3 consecutive `X`
  characters, these characters will be replaced with a random component. If this
  argument is `null` a default `tmp.XXXXXX` template will be used.

  The directory should be a path that exists and the user can write to, if this
  argument is not supplied the default temporary directory will be used.

  A `\RuntimeException` will be thrown if a file cannot be created.

  ```php
  use Cs278\Mktemp\dir;

  var_dump(dir());
  // /tmp/tmp.P9aLnd
  var_dump(dir('output.XXXX.pdf'));
  // /tmp/output.oI7b.pdf
  var_dump(dir(null, '/var/tmp'));
  // /var/tmp/tmp.8uJx
  var_dump(dir('test.XXX.html', '/var/tmp'));
  // /var/tmp/test.9h2.html
  ```
