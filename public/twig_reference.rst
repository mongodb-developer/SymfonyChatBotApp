Twig Extensions Defined by Symfony
==================================

:ref:`Twig <twig-language>` is the template engine used in Symfony applications.
There are tens of `default filters and functions defined by Twig`_, but Symfony
also defines some filters, functions and tags to integrate the various Symfony
components with Twig templates. This article explains them all.

.. tip::

    If these extensions provided by Symfony are not enough, you can
    :ref:`create a custom Twig extension <templates-twig-extension>` to define
    even more filters and functions.

.. _reference-twig-functions:

Functions
---------

.. _reference-twig-function-render:

render
~~~~~~

.. code-block:: twig

    {{ render(uri, options = []) }}

``uri``
    **type**: ``string`` | ``ControllerReference``
``options`` *(optional)*
    **type**: ``array`` **default**: ``[]``

Makes a request to the given internal URI or controller and returns the result.
The render strategy can be specified in the ``strategy`` key of the options.
It's commonly used to :ref:`embed controllers in templates <templates-embed-controllers>`.

.. _reference-twig-function-render-esi:

render_esi
~~~~~~~~~~

.. code-block:: twig

    {{ render_esi(uri, options = []) }}

``uri``
    **type**: ``string`` | ``ControllerReference``
``options`` *(optional)*
    **type**: ``array`` **default**: ``[]``

It's similar to the `render`_ function and defines the same arguments. However,
it generates an ESI tag when :doc:`ESI support </http_cache/esi>` is enabled or
falls back to the behavior of `render`_ otherwise.

.. tip::

    The ``render_esi()`` function is an example of the shortcut functions
    of ``render``. It automatically sets the strategy based on what's given
    in the function name, e.g. ``render_hinclude()`` will use the hinclude.js
    strategy. This works for all ``render_*()`` functions.

fragment_uri
~~~~~~~~~~~~

.. code-block:: twig

    {{ fragment_uri(controller, absolute = false, strict = true, sign = true) }}

``controller``
    **type**: ``ControllerReference``
``absolute`` *(optional)*
    **type**: ``boolean`` **default**: ``false``
``strict`` *(optional)*
    **type**: ``boolean`` **default**: ``true``
``sign`` *(optional)*
    **type**: ``boolean`` **default**: ``true``

Generates the URI of :ref:`a fragment <fragments-path-config>`.

controller
~~~~~~~~~~

.. code-block:: twig

    {{ controller(controller, attributes = [], query = []) }}

``controller``
    **type**: ``string``
``attributes`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``query`` *(optional)*
    **type**: ``array`` **default**: ``[]``

Returns an instance of ``ControllerReference`` to be used with functions
like :ref:`render() <reference-twig-function-render>` and
:ref:`render_esi() <reference-twig-function-render-esi>`.

.. code-block:: twig

    {{ render(controller('App\\Controller\\BlogController:latest', {max: 3})) }}
    {# output: the content returned by the controller method; e.g. a rendered Twig template #}

.. _reference-twig-function-asset:

asset
~~~~~

.. code-block:: twig

    {{ asset(path, packageName = null) }}

``path``
    **type**: ``string``
``packageName`` *(optional)*
    **type**: ``string`` | ``null`` **default**: ``null``

.. code-block:: yaml

    # config/packages/framework.yaml
    framework:
        # ...
        assets:
            packages:
                foo_package:
                    base_path: /avatars

.. code-block:: twig

    {# the image lives at "public/avatars/avatar.png" #}
    {{ asset(path = 'avatar.png', packageName = 'foo_package') }}
    {# output: /avatars/avatar.png #}

Returns the public path of the given asset path (which can be a CSS file, a
JavaScript file, an image path, etc.). This function takes into account where
the application is installed (e.g. in case the project is accessed in a host
subdirectory) and the optional asset package base path.

Symfony provides various cache busting implementations via the
:ref:`assets.version <reference-framework-assets-version>`,
:ref:`assets.version_strategy <reference-assets-version-strategy>`,
and :ref:`assets.json_manifest_path <reference-assets-json-manifest-path>`
configuration options.

.. seealso::

    Read more about :ref:`linking to web assets from templates <templates-link-to-assets>`.

asset_version
~~~~~~~~~~~~~

.. code-block:: twig

    {{ asset_version(path, packageName = null) }}

``path``
    **type**: ``string``
``packageName`` *(optional)*
    **type**: ``string`` | ``null`` **default**: ``null``

Returns the current version of the package, more information in
:ref:`templates-link-to-assets`.

.. _reference-twig-function-csrf-token:

csrf_token
~~~~~~~~~~

.. code-block:: twig

    {{ csrf_token(intention) }}

``intention``
    **type**: ``string`` - an arbitrary string used to identify the token.

Renders a CSRF token. Use this function if you want :doc:`CSRF protection </security/csrf>`
in a regular HTML form not managed by the Symfony Form component.

.. code-block:: twig

    {{ csrf_token('my_form') }}
    {# output: a random alphanumeric string like:
       a.YOosAd0fhT7BEuUCFbROzrvgkW8kpEmBDQ_DKRMUi2o.Va8ZQKt5_2qoa7dLW-02_PLYwDBx9nnWOluUHUFCwC5Zo0VuuVfQCqtngg #}

is_granted
~~~~~~~~~~

.. code-block:: twig

    {{ is_granted(role, object = null) }}

``role``
    **type**: ``string``
``object`` *(optional)*
    **type**: ``object``

Returns ``true`` if the current user has the given role.

Optionally, an object can be passed to be used by the voter. More information
can be found in :ref:`security-template`.

is_granted_for_user
~~~~~~~~~~~~~~~~~~~

.. versionadded:: 7.3

    The ``is_granted_for_user()`` function was introduced in Symfony 7.3.

.. code-block:: twig

    {{ is_granted_for_user(user, attribute, subject = null) }}

``user``
    **type**: ``object``
``attribute``
    **type**: ``string``
``subject`` *(optional)*
    **type**: ``object``

Returns ``true`` if the user is authorized for the specified attribute.

Optionally, an object can be passed to be used by the voter. More information
can be found in :ref:`security-template`.

logout_path
~~~~~~~~~~~

.. code-block:: twig

    {{ logout_path(key = null) }}

``key`` *(optional)*
    **type**: ``string``

Generates a relative logout URL for the given firewall. If no key is provided,
the URL is generated for the current firewall the user is logged into.

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        # ...

        firewalls:
            main:
                # ...
                logout:
                    path: '/logout'
            othername:
                # ...
                logout:
                    path: '/other/logout'

.. code-block:: twig

    {{ logout_path(key = 'main') }}
    {# output: /logout #}

    {{ logout_path(key = 'othername') }}
    {# output: /other/logout #}

logout_url
~~~~~~~~~~

.. code-block:: twig

    {{ logout_url(key = null) }}

``key`` *(optional)*
    **type**: ``string``

Equal to the `logout_path`_ function, but it'll generate an absolute URL
instead of a relative one.

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        # ...

        firewalls:
            main:
                # ...
                logout:
                    path: '/logout'
            othername:
                # ...
                logout:
                    path: '/other/logout'

.. code-block:: twig

    {{ logout_url(key = 'main') }}
    {# output: http://example.org/logout #}

    {{ logout_url(key = 'othername') }}
    {# output: http://example.org/other/logout #}

path
~~~~

.. code-block:: twig

    {{ path(route_name, route_parameters = [], relative = false) }}

``name``
    **type**: ``string``
``parameters`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``relative`` *(optional)*
    **type**: ``boolean`` **default**: ``false``

Returns the relative URL (without the scheme and host) for the given route.
If ``relative`` is enabled, it'll create a path relative to the current path.

.. code-block:: twig

    {# consider that the app defines an 'app_blog' route with the path '/blog/{page}' #}

    {{ path(name = 'app_blog', parameters = {page: 3}, relative = false) }}
    {# output: /blog/3 #}

    {{ path(name = 'app_blog', parameters = {page: 3}, relative = true) }}
    {# output: blog/3 #}

.. seealso::

    Read more about :doc:`Symfony routing </routing>` and about
    :ref:`creating links in Twig templates <templates-link-to-pages>`.

url
~~~

.. code-block:: twig

    {{ url(route_name, route_parameters = [], schemeRelative = false) }}

``name``
    **type**: ``string``
``parameters`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``schemeRelative`` *(optional)*
    **type**: ``boolean`` **default**: ``false``

Returns the absolute URL (with scheme and host) for the given route. If
``schemeRelative`` is enabled, it'll create a scheme-relative URL.

.. code-block:: twig

    {# consider that the app defines an 'app_blog' route with the path '/blog/{page}' #}

    {{ url(name = 'app_blog', parameters = {page: 3}, schemeRelative = false) }}
    {# output: http://example.org/blog/3 #}

    {{ url(name = 'app_blog', parameters = {page: 3}, schemeRelative = true) }}
    {# output: //example.org/blog/3 #}

.. seealso::

    Read more about :doc:`Symfony routing </routing>` and about
    :ref:`creating links in Twig templates <templates-link-to-pages>`.

.. _reference-twig-function-absolute-url:

absolute_url
~~~~~~~~~~~~

.. code-block:: twig

    {{ absolute_url(path) }}

``path``
    **type**: ``string``

Returns the absolute URL (with scheme and host) from the passed relative path. Combine it with the
:ref:`asset() function <reference-twig-function-asset>` to generate absolute URLs
for web assets. Read more about :ref:`Linking to CSS, JavaScript and Image Assets <templates-link-to-assets>`.

.. _reference-twig-function-relative-path:

relative_path
~~~~~~~~~~~~~

.. code-block:: twig

    {{ relative_path(path) }}

``path``
    **type**: ``string``

Returns the relative path from the passed absolute URL. For example, assume
you're on the following page in your app:
``http://example.com/products/hover-board``.

.. code-block:: twig

    {{ relative_path('http://example.com/human.txt') }}
    {# ../human.txt #}

    {{ relative_path('http://example.com/products/products_icon.png') }}
    {# products_icon.png #}

expression
~~~~~~~~~~

Creates an :class:`Symfony\\Component\\ExpressionLanguage\\Expression` related
to the :doc:`ExpressionLanguage component </components/expression_language>`.

.. code-block:: twig

    {{ expression(1 + 2) }}
    {# output: 3 #}

impersonation_path
~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ impersonation_path(identifier) }}

``identifier``
    **type**: ``string``

Generates a URL that you can visit to
:doc:`impersonate a user </security/impersonating_user>`, identified by the
``identifier`` argument.

impersonation_url
~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ impersonation_url(identifier) }}

``identifier``
    **type**: ``string``

It's similar to the `impersonation_path`_ function, but it generates
absolute URLs instead of relative URLs.

impersonation_exit_path
~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ impersonation_exit_path(exitTo = null) }}

``exitTo`` *(optional)*
    **type**: ``string``

Generates a URL that you can visit to exit :doc:`user impersonation </security/impersonating_user>`.
After exiting impersonation, the user is redirected to the current URI. If you
prefer to redirect to a different URI, define its value in the ``exitTo`` argument.

If no user is being impersonated, the function returns an empty string.

impersonation_exit_url
~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ impersonation_exit_url(exitTo = null) }}

``exitTo`` *(optional)*
    **type**: ``string``

It's similar to the `impersonation_exit_path`_ function, but it generates
absolute URLs instead of relative URLs.

.. _reference-twig-function-t:

t
~~~

.. code-block:: twig

    {{ t(message, parameters = [], domain = 'messages')|trans }}

``message``
    **type**: ``string``
``parameters`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``domain`` *(optional)*
    **type**: ``string`` **default**: ``messages``

Creates a ``Translatable`` object that can be passed to the
:ref:`trans filter <reference-twig-filter-trans>`.

.. configuration-block::

    .. code-block:: yaml

        # translations/blog.en.yaml
        message: Hello %name%

    .. code-block:: xml

        <!-- translations/blog.en.xlf -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
            <file source-language="en" datatype="plaintext" original="file.ext">
                <body>
                    <trans-unit id="message">
                        <source>message</source>
                        <target>Hello %name%</target>
                    </trans-unit>
                </body>
            </file>
        </xliff>

    .. code-block:: php

        // translations/blog.en.php
        return [
            'message' => "Hello %name%",
        ];

Using the filter will be rendered as:

.. code-block:: twig

    {{ t(message = 'message', parameters = {'%name%': 'John'}, domain = 'blog')|trans }}
    {# output: Hello John #}

importmap
~~~~~~~~~

Outputs the ``importmap`` & a few other items when using
:doc:`the Asset component </frontend/asset_mapper>`.

Form Related Functions
~~~~~~~~~~~~~~~~~~~~~~

The following functions related to Symfony Forms are also available. They are
explained in the article about :doc:`customizing form rendering </form/form_customization>`:

* :ref:`form() <reference-forms-twig-form>`
* :ref:`form_start() <reference-forms-twig-start>`
* :ref:`form_end() <reference-forms-twig-end>`
* :ref:`form_widget() <reference-forms-twig-widget>`
* :ref:`form_errors() <reference-forms-twig-errors>`
* :ref:`form_label() <reference-forms-twig-label>`
* :ref:`form_help() <reference-forms-twig-help>`
* :ref:`form_row() <reference-forms-twig-row>`
* :ref:`form_rest() <reference-forms-twig-rest>`
* :ref:`field_name() <reference-forms-twig-field-helpers>`
* :ref:`field_id() <reference-forms-twig-field-helpers>`
* :ref:`field_value() <reference-forms-twig-field-helpers>`
* :ref:`field_label() <reference-forms-twig-field-helpers>`
* :ref:`field_help() <reference-forms-twig-field-helpers>`
* :ref:`field_errors() <reference-forms-twig-field-helpers>`
* :ref:`field_choices() <reference-forms-twig-field-helpers>`

.. _reference-twig-filters:

Filters
-------

.. _reference-twig-humanize-filter:

humanize
~~~~~~~~

.. code-block:: twig

    {{ text|humanize }}

``text``
    **type**: ``string``

Transforms the given string into a human readable string (by replacing underscores
with spaces, capitalizing the string, etc.) It's useful e.g. when displaying
the names of PHP properties/variables to end users:

.. code-block:: twig

    {{ 'dateOfBirth'|humanize }}    {# renders: Date of birth #}
    {{ 'DateOfBirth'|humanize }}    {# renders: Date of birth #}
    {{ 'date-of-birth'|humanize }}  {# renders: Date-of-birth #}
    {{ 'date_of_birth'|humanize }}  {# renders: Date of birth #}
    {{ 'date of birth'|humanize }}  {# renders: Date of birth #}
    {{ 'Date Of Birth'|humanize }}  {# renders: Date of birth #}

.. _reference-twig-filter-trans:

trans
~~~~~

.. code-block:: twig

    {{ message|trans(arguments = [], domain = null, locale = null) }}

``message``
    **type**: ``string`` | ``Translatable``
``arguments`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``domain`` *(optional)*
    **type**: ``string`` **default**: ``null``
``locale`` *(optional)*
    **type**: ``string`` **default**: ``null``

Translates the text into the current language. More information in
:ref:`Translation Filters <translation-filters>`.

.. configuration-block::

    .. code-block:: yaml

        # translations/messages.en.yaml
        message: Hello %name%

    .. code-block:: xml

        <!-- translations/messages.en.xlf -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
            <file source-language="en" datatype="plaintext" original="file.ext">
                <body>
                    <trans-unit id="message">
                        <source>message</source>
                        <target>Hello %name%</target>
                    </trans-unit>
                </body>
            </file>
        </xliff>

    .. code-block:: php

        // translations/messages.en.php
        return [
            'message' => "Hello %name%",
        ];

Using the filter will be rendered as:

.. code-block:: twig

    {{ 'message'|trans(arguments = {'%name%': 'John'}, domain = 'messages', locale = 'en') }}
    {# output: Hello John #}

sanitize_html
~~~~~~~~~~~~~

.. code-block:: twig

    {{ body|sanitize_html(sanitizer = "default") }}

``body``
    **type**: ``string``
``sanitizer`` *(optional)*
    **type**: ``string`` **default**: ``"default"``

Sanitizes the text using the HTML Sanitizer component. More information in
:ref:`HTML Sanitizer <html-sanitizer-twig>`.

yaml_encode
~~~~~~~~~~~

.. code-block:: twig

    {{ input|yaml_encode(inline = 0, dumpObjects = false) }}

``input``
    **type**: ``mixed``
``inline`` *(optional)*
    **type**: ``integer`` **default**: ``0``
``dumpObjects`` *(optional)*
    **type**: ``boolean`` **default**: ``false``

Transforms the input into YAML syntax.

The ``inline`` argument is the level where the generated output switches to inline YAML:

.. code-block:: twig

    {% set array = {
        'a': {
            'c': 'e'
        },
        'b': {
            'd': 'f'
        }
    } %}

    {{ array|yaml_encode(inline = 0) }}
    {# output:
       { a: { c: e }, b: { d: f } } #}

    {{ array|yaml_encode(inline = 1) }}
    {# output:
       a: { c: e }
       b: { d: f } #}

The ``dumpObjects`` argument enables the dumping of PHP objects::

    // ...
    $object = new \stdClass();
    $object->foo = 'bar';
    // ...

.. code-block:: twig

    {{ object|yaml_encode(dumpObjects = false) }}
    {# output: null #}

    {{ object|yaml_encode(dumpObjects = true) }}
    {# output: !php/object 'O:8:"stdClass":1:{s:5:"foo";s:7:"bar";}' #}

See :ref:`components-yaml-dump` for more information.

yaml_dump
~~~~~~~~~

.. code-block:: twig

    {{ value|yaml_dump(inline = 0, dumpObjects = false) }}

``value``
    **type**: ``mixed``
``inline`` *(optional)*
    **type**: ``integer`` **default**: ``0``
``dumpObjects`` *(optional)*
    **type**: ``boolean`` **default**: ``false``

Does the same as `yaml_encode() <yaml_encode>`_, but includes the type in
the output.

The ``inline`` argument is the level where the generated output switches to inline YAML:

.. code-block:: twig

    {% set array = {
        'a': {
            'c': 'e'
        },
        'b': {
            'd': 'f'
        }
    } %}

    {{ array|yaml_dump(inline = 0) }}
    {# output:
       %array% { a: { c: e }, b: { d: f } } #}

    {{ array|yaml_dump(inline = 1) }}
    {# output:
       %array% a: { c: e }
       b: { d: f } #}

The ``dumpObjects`` argument enables the dumping of PHP objects::

    // ...
    $object = new \stdClass();
    $object->foo = 'bar';
    // ...

.. code-block:: twig

    {{ object|yaml_dump(dumpObjects = false) }}
    {# output: %object% null #}

    {{ object|yaml_dump(dumpObjects = true) }}
    {# output: %object% !php/object 'O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}' #}

abbr_class
~~~~~~~~~~

.. code-block:: twig

    {{ class|abbr_class }}

``class``
    **type**: ``string``

Generates an ``<abbr>`` element with the short name of a PHP class (the
FQCN will be shown in a tooltip when a user hovers over the element).

.. code-block:: twig

    {{ 'App\\Entity\\Product'|abbr_class }}

The above example will be rendered as:

.. code-block:: html

    <abbr title="App\Entity\Product">Product</abbr>

abbr_method
~~~~~~~~~~~

.. code-block:: twig

    {{ method|abbr_method }}

``method``
    **type**: ``string``

Generates an ``<abbr>`` element using the ``FQCN::method()`` syntax. If
``method`` is ``Closure``, ``Closure`` will be used instead and if ``method``
doesn't have a class name, it's shown as a function (``method()``).

.. code-block:: twig

    {{ 'App\\Controller\\ProductController::list'|abbr_method }}

The above example will be rendered as:

.. code-block:: html

    <abbr title="App\Controller\ProductController">ProductController</abbr>::list()

format_args
~~~~~~~~~~~

.. code-block:: twig

    {{ args|format_args }}

``args``
    **type**: ``array``

Generates a string with the arguments and their types (within ``<em>`` elements).

format_args_as_text
~~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ args|format_args_as_text }}

``args``
    **type**: ``array``

Equal to the `format_args`_ filter, but without using HTML tags.

file_excerpt
~~~~~~~~~~~~

.. code-block:: twig

    {{ file|file_excerpt(line, srcContext = 3) }}

``file``
    **type**: ``string``
``line``
    **type**: ``integer``
``srcContext`` *(optional)*
    **type**: ``integer``

Generates an excerpt of a code file around the given ``line`` number. The
``srcContext`` argument defines the total number of lines to display around the
given line number (use ``-1`` to display the whole file).

Consider the following as the content of ``file.txt``:

.. code-block:: text

    a
    b
    c
    d
    e

.. code-block:: html+twig

    {{ '/path/to/file.txt'|file_excerpt(line = 4, srcContext = 1) }}
    {# output: #}
    <ol start="3">
        <li><a class="anchor" id="line3"></a><code>c</code></li>
        <li class="selected"><a class="anchor" id="line4"></a><code>d</code></li>
        <li><a class="anchor" id="line5"></a><code>e</code></li>
    </ol>

    {{ '/path/to/file.txt'|file_excerpt(line = 1, srcContext = 0) }}
    {# output: #}
    <ol start="1">
        <li class="selected"><a class="anchor" id="line1"></a><code>a</code></li>
    </ol>

format_file
~~~~~~~~~~~

.. code-block:: twig

    {{ file|format_file(line, text = null) }}

``file``
    **type**: ``string``
``line``
    **type**: ``integer``
``text`` *(optional)*
    **type**: ``string`` **default**: ``null``

Generates the file path inside an ``<a>`` element. If the path is inside
the kernel root directory, the kernel root directory path is replaced by
``kernel.project_dir`` (showing the full path in a tooltip on hover).

.. code-block:: html+twig

    {{ '/path/to/file.txt'|format_file(line = 1, text = "my_text") }}
    {# output: #}
    <a href="/path/to/file.txt#L1"
        title="Click to open this file" class="file_link">my_text at line 1
    </a>

    {{ "/path/to/file.txt"|format_file(line = 3) }}
    {# output: #}
    <a href="/path/to/file.txt&amp;line=3"
        title="Click to open this file" class="file_link">/path/to/file.txt at line 3
    </a>

.. tip::

    If you set the :ref:`framework.ide <reference-framework-ide>` option, the
    generated links will change to open the file in that IDE/editor. For example,
    when using PhpStorm, the ``<a href="/path/to/file.txt&amp;line=3"`` link will
    become ``<a href="phpstorm://open?file=/path/to/file.txt&amp;line=3"``.

format_file_from_text
~~~~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {{ text|format_file_from_text }}

``text``
    **type**: ``string``

Uses `format_file`_ to improve the output of default PHP errors.

file_link
~~~~~~~~~

.. code-block:: twig

    {{ file|file_link(line) }}

``file``
    **type**: ``string``
``line``
    **type**: ``integer``

Generates a link to the provided file and line number using
a preconfigured scheme.

.. code-block:: twig

    {{ 'path/to/file/file.txt'|file_link(line = 3) }}
    {# output: file://path/to/file/file.txt#L3 #}

file_relative
~~~~~~~~~~~~~

.. code-block:: twig

    {{ file|file_relative }}

``file``
    **type**: ``string``

It transforms the given absolute file path into a new file path relative to
project's root directory:

.. code-block:: twig

    {{ '/var/www/blog/templates/admin/index.html.twig'|file_relative }}
    {# if project root dir is '/var/www/blog/', it returns 'templates/admin/index.html.twig' #}

If the given file path is out of the project directory, a ``null`` value
will be returned.

.. _reference-twig-filter-serialize:

serialize
~~~~~~~~~

.. code-block:: twig

    {{ object|serialize(format = 'json', context = []) }}

``object``
    **type**: ``mixed``

``format`` *(optional)*
    **type**: ``string``

``context`` *(optional)*
    **type**: ``array``

Accepts any data that can be serialized by the :doc:`Serializer component </serializer>`
and returns a serialized string in the specified ``format``.

For example::

    $object = new \stdClass();
    $object->foo = 'bar';
    $object->content = [];
    $object->createdAt = new \DateTime('2024-11-30');

.. code-block:: twig

    {{ object|serialize(format = 'json', context = {
        'datetime_format': 'D, Y-m-d',
        'empty_array_as_object': true,
    }) }}
    {# output: {"foo":"bar","content":{},"createdAt":"Sat, 2024-11-30"} #}

.. _reference-twig-filter-emojify:

emojify
~~~~~~~

.. versionadded:: 7.1

    The ``emojify`` filter was introduced in Symfony 7.1.

.. code-block:: twig

    {{ text|emojify(catalog = null) }}

``text``
    **type**: ``string``

``catalog`` *(optional)*
    **type**: ``string`` | ``null``

    The emoji set used to generate the textual representation (``slack``,
    ``github``, ``gitlab``, etc.)

It transforms the textual representation of an emoji (e.g. ``:wave:``) into the
actual emoji (👋):

.. code-block:: twig

    {{ ':+1:'|emojify }}                 {# renders: 👍 #}
    {{ ':+1:'|emojify('github') }}       {# renders: 👍 #}
    {{ ':thumbsup:'|emojify('gitlab') }} {# renders: 👍 #}

.. _reference-twig-tags:

Tags
----

.. _reference-twig-tag-form-theme:

form_theme
~~~~~~~~~~

.. code-block:: twig

    {% form_theme form resources %}

``form``
    **type**: ``FormView``
``resources``
    **type**: ``array`` | ``string``

Sets the resources to override the form theme for the given form view instance.
You can use ``_self`` as resources to set it to the current resource. More
information in :doc:`/form/form_customization`.

trans
~~~~~

.. code-block:: twig

    {% trans with vars from domain into locale %}{% endtrans %}

``vars`` *(optional)*
    **type**: ``array`` **default**: ``[]``
``domain`` *(optional)*
    **type**: ``string`` **default**: ``string``
``locale`` *(optional)*
    **type**: ``string`` **default**: ``string``

Renders the translation of the content. More information in :ref:`translation-tags`.

trans_default_domain
~~~~~~~~~~~~~~~~~~~~

.. code-block:: twig

    {% trans_default_domain domain %}

``domain``
    **type**: ``string``

This will set the default domain in the current template.

.. _reference-twig-tag-stopwatch:

stopwatch
~~~~~~~~~

.. code-block:: twig

    {% stopwatch 'event_name' %}...{% endstopwatch %}

This measures the time and memory used to execute some code in the template and
displays it in the Symfony profiler. See :ref:`how to profile Symfony applications <profiling-applications>`.

.. _reference-twig-tests:

Tests
-----

The following tests related to Symfony Forms are available. They are explained
in the article about :doc:`customizing form rendering </form/form_customization>`:

* :ref:`selectedchoice() <form-twig-selectedchoice>`
* :ref:`rootform() <form-twig-rootform>`

Global Variables
----------------

app
~~~

The ``app`` variable is injected automatically by Symfony in all templates and
provides access to lots of useful application information. Read more about the
:ref:`Twig global app variable <twig-app-variable>`.

.. _`default filters and functions defined by Twig`: https://twig.symfony.com/doc/3.x/#reference
