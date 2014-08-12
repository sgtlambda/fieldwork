jannieforms
===========

Setting up a web form can be a pain and often forces you to keep track of seperate definitions for the browser and for the server. Jannieforms takes an object-oriented approach towards building forms and will make your life a lot easier by dealing with all the trivial tasks of markup generation, validation and whatnot.

 - Define entires form using just PHP. No manual HTML or javascript coding involved
 - Sanitizes and validates twice, client-side for convenience, server-side for security

###Getting started

Clone this repo or extract [master.zip](https://github.com/jmversteeg/jannieforms/archive/master.zip) somewhere.
If you're using WordPress, install to `/wp-content/plugins/jannieforms` and activate plugin in WordPress admin for access by other plugins.

Creating a form is as simple as instantiating `JannieForm`:

    $contactform = new JannieForm('contact', '', new JannieFormPostMethod() );

You can create a new field by instantiating any class that extends `JannieFormFieldComponent`:

    $emailField = new JannieTextField('email', 'Email address');

Depending on the capabilities of the input type, the field can be configured in several ways:

    //Adds a validator to the field
    $emailField->addValidator(new JannieFormEmailValidator());
    
    //Attaches the field to the form
    $emailField->addTo($contactForm);

Alternatively, most functions can be used in a chained call, like this:

    $emailField->addValidator(new JannieFormEmailValidator())->addTo($contactForm);

Add a submit button:

    $submit = new JannieButton("submit", "Send", "", JannieButton::TYPE_SUBMIT);
    $submit->setUseShim(false)->addTo($contactForm);

###Todo

 - Use namespaces
 - Use build script (composer)
 - Translate error messages
 - Generate PHPDocs
 - Add centralized `Callback` class for access both direct and through AJAX
 - Add XML form definition parser
 - Add default styles with horizontal row support
 - Load forms on demand
 - Remove low-level configuration of `WRAPPER` and `INPUT` node (perhaps remove `WRAPPER` node altogether and use `box-sizing: border-box;` for input nodes)
 - Convert client-side code to CoffeeScript
 - Implement require.js/AMD support
 - (optionally) Remove `JannieTooltips` and `_JannieForms` from global namespace
