jannieforms
===========

Jannieforms will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for convenience + performance** and **server-side for security**.

### Installation

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

### Creating a simple form

```php
use jannieforms\Form;
use jannieforms\components\TextField;
use jannieforms\validators\JannieFormEmailValidator;
use jannieforms\components\Button;

$contactForm = new Form('contact', '', new JannieFormPostMethod() );

$emailField = new TextField('email', 'Email address');
$emailField
   ->addValidator(new JannieFormEmailValidator())
   ->addTo($contactForm);

$submitButton = new Button('submit', 'Send', 'submit', Button::TYPE_SUBMIT);
$submitButton
   ->addTo($contactForm);
```

#### todo

 - Create composer package that works with [bedrock](https://github.com/roots/bedrock)
 - Error message i18n
 - Add centralized `Callback` and `AjaxEnvironment` classes for callback centralization
 - Add JSON form definition format specs & parser
 - Load forms on demand

#### v3.0 (2015-01-13)

 - Improved class naming
 - Introduced namespaces and autoloader