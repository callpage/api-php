## Installation

You can install **callpage/api-php** via composer or by downloading the source.

#### Via Composer:

**callpage/api-php** is available on Packagist as the
[`callpage/api-php`](http://packagist.org/packages/callpage/api-php) package.

#### Via ZIP file:

[Click here to download the source
(.zip)](https://github.com/callpage/api-php/zipball/master) which includes all
dependencies.

Once you download the library, move the **callpage/api-php** folder to your project
directory and then include the library file:

    require '/path/to/callpage/api-php/src/autoload.php';

and you're good to go!

## A Brief Introduction

With the callpage-php library, we've simplified interaction with the
CallPage REST API. No need to manually create URLS or parse XML/JSON.
You now interact with resources directly. 

## Quickstart

### Make a Call

Make a call to all of available managers. If managers are busy or after working hours or your account balance is not enough, the call won't be proceed, the SMS will be sent instead.

```php
<?php
// Install the library via PEAR or download the .zip file to your project folder.
// This line loads the library in case without composer
require('/path/to/callpage/api-php/autoload.php');

$apiKey = "ACXXXXXX"; // Your API Key from http://callpage.io/settings/api
$widgetId = "YYYYYY"; // Your Widget ID from http://callpage.io/widgets

$callpage = new \Callpage\ApiPhp\Callpage($apiKey, $widgetId); //callpage API instance

//wraps method in try catch block
try {
    $callpage->callOrSchedule('+48570570570');
    
    //call was successfully done, your logic come here
    
}
catch (\Exception $e) {
    echo 'Something goes wrong! The reason: ' . $e->getMessage();
}


```

## Full Documentation

Coming soon

## Prerequisites

* PHP >= 5.4.0
* The PHP JSON extension
* The PHP Curl extension

# Getting help

If you need help installing or using the library, please contact Callpage Support at support@callpage.com first. CallPage's Support staff are well-versed in all of the CallPage Helper Libraries, and usually reply within 24 hours.

If you've instead found a bug in the library or would like new features added, go ahead and open issues or pull requests against this repo!
