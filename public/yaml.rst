The Yaml Component
==================

    The Yaml component loads and dumps YAML files.

What is It?
-----------

The Symfony Yaml component parses YAML strings to convert them to PHP arrays.
It is also able to convert PHP arrays to YAML strings.

`YAML`_, *YAML Ain't Markup Language*, is a human friendly data serialization
standard for all programming languages. YAML is a great format for your
configuration files. YAML files are as expressive as XML files and as readable
as INI files.

.. tip::

    Learn more about :doc:`YAML specifications </reference/formats/yaml>`.

Installation
------------

.. code-block:: terminal

    $ composer require symfony/yaml

.. include:: /components/require_autoload.rst.inc

Why?
----

Fast
~~~~

One of the goals of Symfony Yaml is to find the right balance between speed and
features. It supports just the needed features to handle configuration files.
Notable lacking features are: document directives, multi-line quoted messages,
compact block collections and multi-document files.

Real Parser
~~~~~~~~~~~

It supports a real parser and is able to parse a large subset of the YAML
specification, for all your configuration needs. It also means that the parser
is pretty robust, easy to understand, and simple enough to extend.

Clear Error Messages
~~~~~~~~~~~~~~~~~~~~

Whenever you have a syntax problem with your YAML files, the library outputs a
helpful message with the filename and the line number where the problem
occurred. It eases the debugging a lot.

Dump Support
~~~~~~~~~~~~

It is also able to dump PHP arrays to YAML with object support, and inline
level configuration for pretty outputs.

Types Support
~~~~~~~~~~~~~

It supports most of the YAML built-in types like dates, integers, octal numbers,
booleans, and much more...

Full Merge Key Support
~~~~~~~~~~~~~~~~~~~~~~

Full support for references, aliases, and full merge key. Don't repeat
yourself by referencing common configuration bits.

.. _using-the-symfony2-yaml-component:

Using the Symfony YAML Component
--------------------------------

The Symfony Yaml component consists of two main classes:
one parses YAML strings (:class:`Symfony\\Component\\Yaml\\Parser`), and the
other dumps a PHP array to a YAML string
(:class:`Symfony\\Component\\Yaml\\Dumper`).

On top of these two classes, the :class:`Symfony\\Component\\Yaml\\Yaml` class
acts as a thin wrapper that simplifies common uses.

Reading YAML Contents
~~~~~~~~~~~~~~~~~~~~~

The :method:`Symfony\\Component\\Yaml\\Yaml::parse` method parses a YAML
string and converts it to a PHP array::

    use Symfony\Component\Yaml\Yaml;

    $value = Yaml::parse("foo: bar");
    // $value = ['foo' => 'bar']

If an error occurs during parsing, the parser throws a
:class:`Symfony\\Component\\Yaml\\Exception\\ParseException` exception
indicating the error type and the line in the original YAML string where the
error occurred::

    use Symfony\Component\Yaml\Exception\ParseException;

    try {
        $value = Yaml::parse('...');
    } catch (ParseException $exception) {
        printf('Unable to parse the YAML string: %s', $exception->getMessage());
    }

Reading YAML Files
~~~~~~~~~~~~~~~~~~

The :method:`Symfony\\Component\\Yaml\\Yaml::parseFile` method parses the YAML
contents of the given file path and converts them to a PHP value::

    use Symfony\Component\Yaml\Yaml;

    $value = Yaml::parseFile('/path/to/file.yaml');

If an error occurs during parsing, the parser throws a ``ParseException`` exception.

.. _components-yaml-dump:

Writing YAML Files
~~~~~~~~~~~~~~~~~~

The :method:`Symfony\\Component\\Yaml\\Yaml::dump` method dumps any PHP
array to its YAML representation::

    use Symfony\Component\Yaml\Yaml;

    $array = [
        'foo' => 'bar',
        'bar' => ['foo' => 'bar', 'bar' => 'baz'],
    ];

    $yaml = Yaml::dump($array);

    file_put_contents('/path/to/file.yaml', $yaml);

If an error occurs during the dump, the parser throws a
:class:`Symfony\\Component\\Yaml\\Exception\\DumpException` exception.

.. _array-expansion-and-inlining:

Expanded and Inlined Arrays
...........................

The YAML format supports two kind of representation for arrays, the expanded
one, and the inline one. By default, the dumper uses the expanded
representation:

.. code-block:: yaml

    foo: bar
    bar:
        foo: bar
        bar: baz

The second argument of the :method:`Symfony\\Component\\Yaml\\Yaml::dump`
method customizes the level at which the output switches from the expanded
representation to the inline one::

    echo Yaml::dump($array, 1);

.. code-block:: yaml

    foo: bar
    bar: { foo: bar, bar: baz }

.. code-block:: php

    echo Yaml::dump($array, 2);

.. code-block:: yaml

    foo: bar
    bar:
        foo: bar
        bar: baz

Indentation
...........

By default, the YAML component will use 4 spaces for indentation. This can be
changed using the third argument as follows::

    // uses 8 spaces for indentation
    echo Yaml::dump($array, 2, 8);

.. code-block:: yaml

    foo: bar
    bar:
            foo: bar
            bar: baz

Numeric Literals
................

Long numeric literals, being integer, float or hexadecimal, are known for their
poor readability in code and configuration files. That's why YAML files allow to
add underscores to improve their readability:

.. code-block:: yaml

    parameters:
        credit_card_number: 1234_5678_9012_3456
        long_number: 10_000_000_000
        pi: 3.14159_26535_89793
        hex_words: 0x_CAFE_F00D

During the parsing of the YAML contents, all the ``_`` characters are removed
from the numeric literal contents, so there is not a limit in the number of
underscores you can include or the way you group contents.

.. _yaml-flags:

Advanced Usage: Flags
---------------------

.. _objects-for-mappings:

Object Parsing and Dumping
~~~~~~~~~~~~~~~~~~~~~~~~~~

You can dump objects by using the ``DUMP_OBJECT`` flag::

    $object = new \stdClass();
    $object->foo = 'bar';

    $dumped = Yaml::dump($object, 2, 4, Yaml::DUMP_OBJECT);
    // !php/object 'O:8:"stdClass":1:{s:5:"foo";s:7:"bar";}'

And parse them by using the ``PARSE_OBJECT`` flag::

    $parsed = Yaml::parse($dumped, Yaml::PARSE_OBJECT);
    var_dump(is_object($parsed)); // true
    echo $parsed->foo; // bar

The YAML component uses PHP's ``serialize()`` method to generate a string
representation of the object.

.. danger::

    Object serialization is specific to this implementation, other PHP YAML
    parsers will likely not recognize the ``php/object`` tag and non-PHP
    implementations certainly won't - use with discretion!

Parsing and Dumping Objects as Maps
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can dump objects as Yaml maps by using the ``DUMP_OBJECT_AS_MAP`` flag::

    $object = new \stdClass();
    $object->foo = 'bar';

    $dumped = Yaml::dump(['data' => $object], 2, 4, Yaml::DUMP_OBJECT_AS_MAP);
    // $dumped = "data:\n    foo: bar"

And parse them by using the ``PARSE_OBJECT_FOR_MAP`` flag::

    $parsed = Yaml::parse($dumped, Yaml::PARSE_OBJECT_FOR_MAP);
    var_dump(is_object($parsed)); // true
    var_dump(is_object($parsed->data)); // true
    echo $parsed->data->foo; // bar

The YAML component uses PHP's ``(array)`` casting to generate a string
representation of the object as a map.

.. _invalid-types-and-object-serialization:

Handling Invalid Types
~~~~~~~~~~~~~~~~~~~~~~

By default, the parser will encode invalid types as ``null``. You can make the
parser throw exceptions by using the ``PARSE_EXCEPTION_ON_INVALID_TYPE``
flag::

    $yaml = '!php/object \'O:8:"stdClass":1:{s:5:"foo";s:7:"bar";}\'';
    Yaml::parse($yaml, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE); // throws an exception

Similarly you can use ``DUMP_EXCEPTION_ON_INVALID_TYPE`` when dumping::

    $data = new \stdClass(); // by default objects are invalid.
    Yaml::dump($data, 2, 4, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE); // throws an exception

Date Handling
~~~~~~~~~~~~~

By default, the YAML parser will convert unquoted strings which look like a
date or a date-time into a Unix timestamp; for example ``2016-05-27`` or
``2016-05-27T02:59:43.1Z`` (`ISO-8601`_)::

    Yaml::parse('2016-05-27'); // 1464307200

You can make it convert to a ``DateTime`` instance by using the ``PARSE_DATETIME``
flag::

    $date = Yaml::parse('2016-05-27', Yaml::PARSE_DATETIME);
    var_dump(get_class($date)); // DateTime

Dumping Multi-line Literal Blocks
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In YAML, multiple lines can be represented as literal blocks. By default, the
dumper will encode multiple lines as an inline string::

    $string = ["string" => "Multiple\nLine\nString"];
    $yaml = Yaml::dump($string);
    echo $yaml; // string: "Multiple\nLine\nString"

You can make it use a literal block with the ``DUMP_MULTI_LINE_LITERAL_BLOCK``
flag::

    $string = ["string" => "Multiple\nLine\nString"];
    $yaml = Yaml::dump($string, 2, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    echo $yaml;
    //  string: |
    //       Multiple
    //       Line
    //       String

Parsing PHP Constants
~~~~~~~~~~~~~~~~~~~~~

By default, the YAML parser treats the PHP constants included in the contents as
regular strings. Use the ``PARSE_CONSTANT`` flag and the special ``!php/const``
syntax to parse them as proper PHP constants::

    $yaml = '{ foo: PHP_INT_SIZE, bar: !php/const PHP_INT_SIZE }';
    $parameters = Yaml::parse($yaml, Yaml::PARSE_CONSTANT);
    // $parameters = ['foo' => 'PHP_INT_SIZE', 'bar' => 8];

Parsing PHP Enumerations
~~~~~~~~~~~~~~~~~~~~~~~~

The YAML parser supports `PHP enumerations`_, both unit and backed enums.
By default, they are parsed as regular strings. Use the ``PARSE_CONSTANT`` flag
and the special ``!php/enum`` syntax to parse them as proper PHP enums::

    enum FooEnum: string
    {
        case Foo = 'foo';
        case Bar = 'bar';
    }

    // ...

    $yaml = '{ foo: FooEnum::Foo, bar: !php/enum FooEnum::Foo }';
    $parameters = Yaml::parse($yaml, Yaml::PARSE_CONSTANT);
    // the value of the 'foo' key is a string because it missed the `!php/enum` syntax
    // $parameters = ['foo' => 'FooEnum::Foo', 'bar' => FooEnum::Foo];

    $yaml = '{ foo: FooEnum::Foo, bar: !php/enum FooEnum::Foo->value }';
    $parameters = Yaml::parse($yaml, Yaml::PARSE_CONSTANT);
    // the value of the 'foo' key is a string because it missed the `!php/enum` syntax
    // $parameters = ['foo' => 'FooEnum::Foo', 'bar' => 'foo'];

You can also use ``!php/enum`` to get all the enumeration cases by only
giving the enumeration FQCN::

    enum FooEnum: string
    {
        case Foo = 'foo';
        case Bar = 'bar';
    }

    // ...

    $yaml = '{ bar: !php/enum FooEnum }';
    $parameters = Yaml::parse($yaml, Yaml::PARSE_CONSTANT);
    // $parameters = ['bar' => ['foo', 'bar']];

.. versionadded:: 7.1

    The support for using the enum FQCN without specifying a case
    was introduced in Symfony 7.1.

Parsing and Dumping of Binary Data
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Non UTF-8 encoded strings are dumped as base64 encoded data::

    $imageContents = file_get_contents(__DIR__.'/images/logo.png');

    $dumped = Yaml::dump(['logo' => $imageContents]);
    // logo: !!binary iVBORw0KGgoAAAANSUhEUgAAA6oAAADqCAY...

Binary data is automatically parsed if they include the ``!!binary`` YAML tag::

    $dumped = 'logo: !!binary iVBORw0KGgoAAAANSUhEUgAAA6oAAADqCAY...';
    $parsed = Yaml::parse($dumped);
    $imageContents = $parsed['logo'];

Parsing and Dumping Custom Tags
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In addition to the built-in support of tags like ``!php/const`` and
``!!binary``, you can define your own custom YAML tags and parse them with the
``PARSE_CUSTOM_TAGS`` flag::

    $data = "!my_tag { foo: bar }";
    $parsed = Yaml::parse($data, Yaml::PARSE_CUSTOM_TAGS);
    // $parsed = Symfony\Component\Yaml\Tag\TaggedValue('my_tag', ['foo' => 'bar']);
    $tagName = $parsed->getTag();    // $tagName = 'my_tag'
    $tagValue = $parsed->getValue(); // $tagValue = ['foo' => 'bar']

If the contents to dump contain :class:`Symfony\\Component\\Yaml\\Tag\\TaggedValue`
objects, they are automatically transformed into YAML tags::

    use Symfony\Component\Yaml\Tag\TaggedValue;

    $data = new TaggedValue('my_tag', ['foo' => 'bar']);
    $dumped = Yaml::dump($data);
    // $dumped = '!my_tag { foo: bar }'

Dumping Null Values
~~~~~~~~~~~~~~~~~~~

The official YAML specification uses both ``null`` and ``~`` to represent null
values. This component uses ``null`` by default when dumping null values but
you can dump them as ``~`` with the ``DUMP_NULL_AS_TILDE`` flag::

    $dumped = Yaml::dump(['foo' => null]);
    // foo: null

    $dumped = Yaml::dump(['foo' => null], 2, 4, Yaml::DUMP_NULL_AS_TILDE);
    // foo: ~

Another valid representation of the ``null`` value is an empty string. You can
use the ``DUMP_NULL_AS_EMPTY`` flag to dump null values as empty strings::

    $dumped = Yaml::dump(['foo' => null], 2, 4, Yaml::DUMP_NULL_AS_EMPTY);
    // foo:

.. versionadded:: 7.3

    The ``DUMP_NULL_AS_EMPTY`` flag was introduced in Symfony 7.3.

Dumping Numeric Keys as Strings
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default, digit-only array keys are dumped as integers. You can use the
``DUMP_NUMERIC_KEY_AS_STRING`` flag if you want to dump string-only keys::

    $dumped = Yaml::dump([200 => 'foo']);
    // 200: foo

    $dumped = Yaml::dump([200 => 'foo'], 2, 4, Yaml::DUMP_NUMERIC_KEY_AS_STRING);
    // '200': foo

Dumping Double Quotes on Values
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default, only unsafe string values are enclosed in double quotes (for example,
if they are reserved words or contain newlines and spaces). Use the
``DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES`` flag to add double quotes to all string values::

    $dumped = Yaml::dump([
        'foo' => 'bar', 'some foo' => 'some bar', 'x' => 3.14, 'y' => true, 'z' => null,
    ]);
    // foo: bar, 'some foo': 'some bar', x: 3.14, 'y': true, z: null

    $dumped = Yaml::dump([
        'foo' => 'bar', 'some foo' => 'some bar', 'x' => 3.14, 'y' => true, 'z' => null,
    ], 2, 4, Yaml::DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES);
    // "foo": "bar", "some foo": "some bar", "x": 3.14, "y": true, "z": null

.. versionadded:: 7.3

    The ``Yaml::DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES`` flag was introduced in Symfony 7.3.

Dumping Collection of Maps
~~~~~~~~~~~~~~~~~~~~~~~~~~

When the YAML component dumps collections of maps, it uses a hyphen on a separate
line as a delimiter:

.. code-block:: yaml

    planets:
      -
        name: Mercury
        distance: 57910000
      -
        name: Jupiter
        distance: 778500000

To produce a more compact output where the delimiter is included within the map,
use the ``Yaml::DUMP_COMPACT_NESTED_MAPPING`` flag:

.. code-block:: yaml

    planets:
      - name: Mercury
        distance: 57910000
      - name: Jupiter
        distance: 778500000

.. versionadded:: 7.3

    The ``Yaml::DUMP_COMPACT_NESTED_MAPPING`` flag was introduced in Symfony 7.3.

Syntax Validation
~~~~~~~~~~~~~~~~~

The syntax of YAML contents can be validated through the CLI using the
:class:`Symfony\\Component\\Yaml\\Command\\LintCommand` command.

First, install the Console component:

.. code-block:: terminal

    $ composer require symfony/console

Create a console application with ``lint:yaml`` as its only command::

    // lint.php
    use Symfony\Component\Console\Application;
    use Symfony\Component\Yaml\Command\LintCommand;

    (new Application('yaml/lint'))
        ->add(new LintCommand())
        ->getApplication()
        ->setDefaultCommand('lint:yaml', true)
        ->run();

Then, execute the script for validating contents:

.. code-block:: terminal

    # validates a single file
    $ php lint.php path/to/file.yaml

    # or validates multiple files
    $ php lint.php path/to/file1.yaml path/to/file2.yaml

    # or all the files in a directory
    $ php lint.php path/to/directory

    # or all the files in multiple directories
    $ php lint.php path/to/directory1 path/to/directory2

    # or contents passed to STDIN
    $ cat path/to/file.yaml | php lint.php

    # you can also exclude one or more files from linting
    $ php lint.php path/to/directory --exclude=path/to/directory/foo.yaml --exclude=path/to/directory/bar.yaml

The result is written to STDOUT and uses a plain text format by default.
Add the ``--format`` option to get the output in JSON format:

.. code-block:: terminal

    $ php lint.php path/to/file.yaml --format=json

.. tip::

    The linting command will also report any deprecations in the checked
    YAML files. This may for example be useful for recognizing deprecations of
    contents of YAML files during automated tests.

.. _`YAML`: https://yaml.org/
.. _`ISO-8601`: https://www.iso.org/iso-8601-date-and-time-format.html
.. _`PHP enumerations`: https://www.php.net/manual/en/language.types.enumerations.php
