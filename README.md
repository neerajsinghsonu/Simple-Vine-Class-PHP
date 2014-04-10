Simple-Vine-Class-PHP
=====================

This is a PHP class for Vine. This class help you to get data from Vine app and you can search any tag.

How to Use
==============

Just set your Vine user name and password in Class.Vine.php

```php
/**
	 * Vine User Login (Email || Name)
	 *
	 * @var string
	 * @access protected
	 */
	protected $loginName = 'vine-user-login-email@gmail.com';

	/**
	 * Vine User Login Password
	 *
	 * @var string
	 * @access protected
	 */
	protected $loginPassword = 'mysecretpass';
	
```

Then, in page

```php
// include Class.Vine.php
require_once 'Class.Vine.php';

// create Vine Class Object
$vineObject = new Vine ();

// now get tag data
$result = $vineObject->searchTag ( 'Your-Search-Tag', 0, 10 );

// check array data
print_r($result);
```

that's it... :)
