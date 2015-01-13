jannieforms
===========

Jannieforms will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for convenience + performance** and **server-side for security**.

###Getting started

```bash
# navigate to your plugin folder
cd wp-content/plugins

# or if you're using bedrock
cd app/mu-plugins

git clone https://github.com/jmversteeg/jannieforms.git
cd jannieforms
npm install
bower install
gulp
```

Creating a form is as simple as instantiating `jannieforms\Form`:

```php
use jannieforms\Form;

$contactform = new Form('contact', '', new JannieFormPostMethod() );
```

You can create a new field by instantiating any class that extends `jannieforms\components\Field`:

```php
use jannieforms\components\TextField;

$emailField = new TextField('email', 'Email address');
```

Configure the field and attach it to the form:

```php
use jannieforms\validators\JannieFormEmailValidator;

$emailField
   ->addValidator(new JannieFormEmailValidator())
   ->addTo($contactForm);
```

Add a submit button:
    
```php
use jannieforms\components\Button;

$submit = new Button("submit", "Send", "", Button::TYPE_SUBMIT);
$submit
   ->setUseShim(false)
   ->addTo($contactForm);
```

#### todo

 - Create composer package that works with [bedrock](https://github.com/roots/bedrock)
 - Error message i18n
 - Add centralized `Callback` and `AjaxEnvironment` classes for callback centralization
 - Add JSON form definition format specs & parser
 - Load forms on demand
 - Tidy up code
 - Drop jQuery requirement

#### v3.0 (2015-01-13)

 - Improved class naming
 - Introduced namespaces and autoloader