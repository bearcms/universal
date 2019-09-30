# Bear CMS Universal

A client package for Bear CMS. It's a perfect choice for developers who want to provide content editing functionality while taking full ownership of the output code. Also suitable for building websites from HTML templates.

## Installation

### Install via Composer
```shell
composer require bearcms/universal
```
The library's autoloader will be automatically run when including `vendor/autoload.php`.

You can quick start a new project by running
```
composer create-project bearcms/universal-app [my-app-name]
```

### Download a ZIP file
Download the latest release ZIP file from from the [releases page](https://github.com/bearcms/universal/releases). Then unzip and include the library's autoloader.
```php
include 'path-to-bearcms-universal-client/autoload.php';
```

### Download a PHAR file
Download the latest release PHAR file from from the [releases page](https://github.com/bearcms/universal/releases). Then include itclude it in your project.
```php
include 'path-to-bearcms-universal-client.phar';
```

## Usage
The first step is to create and configure the BearCMS\Universal instance.

```php
$bearCMS = new BearCMS\Universal([
    'dataDir' => __DIR__ . '/../bearcms/data', // The directory where the website data will be stored
    'logsDir' => __DIR__ . '/../bearcms/logs', // The directory for logs
    'appSecretKey' => 'TODO' // Get your app secret key from https://bearcms.com/
]);
```

Then use `captureStart()` and `captureSend()` to automatically capture the output HTML and send it to the Bear CMS client.

```php
$bearCMS->captureStart();
// Place the HTML code of your page here (or include it from other file)
$bearCMS->captureSend();
```

Alternatively you can use ...

```php
$response = $bearCMS->makeResponse('SOME HTML CODE');
$bearCMS->send($response);
```

Any `<bearcms...>` HTML tags will be automatically updated for preview or editing (if there is a logged in administrator).

A full list of the supported HTML tags is available at [https://bearcms.com/support/use-custom-elements/](https://bearcms.com/support/use-custom-elements/)

## License
This project is licensed under the MIT License. See the [license file](https://github.com/bearcms/universal/blob/master/LICENSE) for more information.

## Author
This package is created and maintained by the Bear CMS team. Feel free to contact us at [support@bearcms.com](mailto:support@bearcms.com) or [bearcms.com](https://bearcms.com/).
