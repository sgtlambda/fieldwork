fieldwork
===========
**Web forms for cool people**

fieldwork will make your life easier by dealing with the trivial tasks of building web forms such as markup generation, validation and sanitization.

 - Define entire forms using **PHP only**. All HTML and JavaScript code will be generated for you.
 - Sanitizes and validates **client-side for convenience + performance** and **server-side for security**.

### Installation

```bash
cd wp-content/plugins
git clone https://github.com/jmversteeg/fieldwork.git
```

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
   ->setGlyphIcon('right-open')
   ->addTo($contactForm);

// Process the form
$contactForm->process();

if($contactForm->isSubmitted())
    echo 'Your email address is ' . $contactForm->v('email');
else
    echo $contactForm->getHTML();
```

#### todo

 - Error message i18n
 - Custom layout markups (perhaps through a proprietary HTML derivative)
 - Add ability to run sanitization, validation and callbacks through AJAX
 - Complete test coverage
 - Add continuous integration
 - 
 
#### HEAD
 - Moved the front-end assets into a separate repo
 - Added NumberSanitizer
 - Added materialize.css compatible markup generation
 - Fixed some bugs
 
#### 4.2.0 (2015-03-19)
 - Made the real-time validation less intrusive (if a field is empty on blur and its value has not changed, it is not validated)
 
#### 4.1.0 (2015-03-17)
 - Various bugfixes
 - Documentation improvement
 - Added some utility methods

#### 4.0.2 (2015-02-12)
 - Add PHPunit test framework
 - Remove foreign error message strings
 - Use external libraries for email and IBAN validation

#### 4.0.1 (2015-02-11)
 - Move /lib to /src/fieldwork

#### 4.0.0 (2015-02-10)
 - Rename the library and all related classes and namespaces to "Fieldwork"

#### 3.0.0 (2015-01-13)

 - Improved class naming
 - Introduced namespaces and autoloader
