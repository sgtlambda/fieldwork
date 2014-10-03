jannieforms
===========

Setting up a web form can be a pain and often forces you to keep track of seperate definitions for the browser and for the server. Jannieforms takes an object-oriented approach towards building forms and will make your life a lot easier by dealing with trivial tasks such as markup generation, validation and sanitization.

 - Define entire forms using just PHP. No manual HTML or javascript coding involved
 - Sanitizes and validates twice, client-side for convenience and performance, server-side for security

###Getting started

Clone this repo or extract [master.zip](https://github.com/jmversteeg/jannieforms/archive/master.zip) somewhere.
If you're using WordPress, install to `/wp-content/plugins/jannieforms` and activate plugin in WordPress admin for access by other plugins.

Creating a form is as simple as instantiating `JannieForm`:

    $contactform = new JannieForm('contact', '', new JannieFormPostMethod() );

You can create a new field by instantiating any class that extends `JannieFormFieldComponent`:

    $emailField = new JannieTextField('email', 'Email address');

Configure the field and attach it to the form:

    $emailField
       ->addValidator(new JannieFormEmailValidator())
       ->addTo($contactForm);

Add a submit button:

    $submit = new JannieButton("submit", "Send", "", JannieButton::TYPE_SUBMIT);
    $submit
       ->setUseShim(false)
       ->addTo($contactForm);

###Todo

 - Improve class naming
 - Use namespaces
 - Use build script (composer)
 - Error message i18n
 - Generate PHPDocs
 - Add centralized `Callback` class for access both direct and through AJAX
 - Add JSON form definition format specs & parser to go along with it
 - Load forms on demand
 - Use CoffeeScript
 - require.js/AMD support
 - Drop jQuery requirement
