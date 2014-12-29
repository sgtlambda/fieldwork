jannieforms
===========

Jannieforms will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for user convenience** and **server-side for security**.

###Getting started

1. Clone this repo or download and extract [master.zip](https://github.com/jmversteeg/jannieforms/archive/master.zip).
2. (optional) If you're using WordPress, install to `/wp-content/plugins/jannieforms` and activate plugin through WordPress back-end.

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
 - Create composer package
 - ORM integration
 - Error message i18n
 - Add centralized `Callback` class for access both direct and through AJAX
 - Add JSON form definition format specs & parser to go along with it
 - Load forms on demand
 - Tidy up code
 - Drop jQuery requirement
