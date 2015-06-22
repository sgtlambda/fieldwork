fieldwork
===========
**Web forms for cool people**

[![Build Status][travis-image]][travis-url]
[![Code Quality][scrutinizer-g-image]][scrutinizer-g-url]
[![Code Coverage][coveralls-image]][coveralls-url]

fieldwork will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for convenience + performance** and **server-side for security**.

### Creating a simple form

```php
use fieldwork\Form;
use fieldwork\components\TextField;
use fieldwork\components\Button;
use fieldwork\validators\EmailFieldValidator;

// Instantiate a new form
$contactForm = new Form('contactform');

// Add a text field with validation
$emailField = new TextField('email', 'Email address');
$emailField
   ->addValidator(new EmailFieldValidator())
   ->addTo($contactForm);

// Add a submit button
$submitButton = new Button('submit', 'Send', 'submit', Button::TYPE_SUBMIT);
$submitButton
   ->addTo($contactForm);

// Process the form
$contactForm->process();

if($contactForm->isSubmitted())
    echo 'Your email address is ' . $contactForm->v('email');
else
    echo $contactForm->getHTML();
```

#### TODO

 - Error message i18n
 - Tighter AJAX integration / API
 - Complete test coverage
 
#### HEAD

 - Moved the front-end assets into a separate repo
 - Added NumberSanitizer
 - Added materialize.css compatible markup generation
 - Fixed some bugs

[travis-image]: https://img.shields.io/travis/jmversteeg/fieldwork.svg?style=flat-square
[travis-url]: https://travis-ci.org/jmversteeg/fieldwork

[scrutinizer-g-image]: https://img.shields.io/scrutinizer/g/jmversteeg/fieldwork.svg?style=flat-square
[scrutinizer-g-url]: https://scrutinizer-ci.com/g/jmversteeg/fieldwork/

[coveralls-image]: https://img.shields.io/coveralls/jmversteeg/fieldwork.svg?style=flat-square
[coveralls-url]: https://coveralls.io/r/jmversteeg/fieldwork