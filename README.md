PHP mktemp
==========

Create temporary files and directories in PHP, safely.

Sadly PHP is deficient when it comes to creating temporary files and directories
in a safe and secure manner. This library aims to address these short-comings.

Usage
-----

* `Cs278\Mktemp\temporaryFile(string|null $template, string|null $dir)`

  Create a temporary file with a supplied template for the name.

  The template should be a string containing a sequence of 3 consecutive `X`
  characters, these characters will be replaced with a random component. If this
  argument is `null` a default `tmp.XXXXXX` template will be used.

  The directory should be a path that exists and the user can write to, if this
  argument is not supplied the default temporary directory will be used.

  A `\RuntimeException` will be thrown if a file cannot be created.

  ```php
  use Cs278\Mktemp\temporaryFile;

  var_dump(temporaryFile());
  // /tmp/tmp.P9aLnd
  var_dump(temporaryFile('output.XXXX.pdf'));
  // /tmp/output.oI7b.pdf
  var_dump(temporaryFile(null, '/var/tmp'));
  // /var/tmp/tmp.8uJx
  var_dump(temporaryFile('test.XXX.html', '/var/tmp'));
  // /var/tmp/test.9h2.html
  ```

* `Cs278\Mktemp\temporaryDir(string|null $template, string|null $dir)`

  Create a temporary directory with a supplied template for the name.

  The template should be a string containing a sequence of 3 consecutive `X`
  characters, these characters will be replaced with a random component. If this
  argument is `null` a default `tmp.XXXXXX` template will be used.

  The directory should be a path that exists and the user can write to, if this
  argument is not supplied the default temporary directory will be used.

  A `\RuntimeException` will be thrown if a file cannot be created.

  ```php
  use Cs278\Mktemp\temporaryDir;

  var_dump(temporaryDir());
  // /tmp/tmp.P9aLnd
  var_dump(temporaryDir('output.XXXX.pdf'));
  // /tmp/output.oI7b.pdf
  var_dump(temporaryDir(null, '/var/tmp'));
  // /var/tmp/tmp.8uJx
  var_dump(temporaryDir('test.XXX.html', '/var/tmp'));
  // /var/tmp/test.9h2.html
  ```
