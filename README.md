fieldwork
===========
**Web forms for cool people**

Fieldwork will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for convenience + performance** and **server-side for security**.

### Installation

```bash
# navigate to your plugin folder
cd wp-content/plugins

# or if you're using bedrock
cd app/mu-plugins

git clone https://github.com/jmversteeg/Fieldwork.git
cd fieldwork
npm install
bower install
gulp
```

### Creating a simple form

```php
use fieldwork\Form;
use fieldwork\components\TextField;
use fieldwork\validators\JannieFormEmailValidator;
use fieldwork\components\Button;

// Instantiate a new form
$contactForm = new Form('contact', '', new JannieFormPostMethod() );

// Add a text field with validation
$emailField = new TextField('email', 'Email address');
$emailField
   ->addValidator(new JannieFormEmailValidator())
   ->addTo($contactForm);

// Add a submit button
$submitButton = new Button('submit', 'Send', 'submit', Button::TYPE_SUBMIT);
$submitButton
   ->setGlyphIcon('right-open')
   ->addTo($contactForm);

// Output the form
echo $contactForm->getHTML();
```

#### todo

 - Create composer package
 - (Error message) i18n
 - Add `Callback` class for callback centralization
 - Add JSON form definition format specs & parser OR parse from HTML

#### v3.0.1 (2015-02-10)
 - Rename the library to "Fieldwork"

#### v3.0.0 (2015-01-13)

 - Improved class naming
 - Introduced namespaces and autoloader
