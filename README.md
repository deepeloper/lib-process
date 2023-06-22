# PHP-process Lock library
[![Latest Stable Version](https://img.shields.io/packagist/v/deepeloper/lib-process.svg?style=flat-square)](https://packagist.org/packages/deepeloper/lib-process)
[![PHP Version](https://img.shields.io/packagist/php-v/deepeloper/lib-process)](https://www.php.net/)
[![Packagist version](https://img.shields.io/packagist/v/deepeloper/lib-process)](https://packagist.org/packages/deepeloper/lib-process)
[![GitHub license](https://img.shields.io/github/license/deepeloper/lib-process.svg)](https://github.com/deepeloper/lib-process/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues-raw/deepeloper/lib-process.svg)](https://github.com/deepeloper/lib-process/issues)

## Installing
Run `composer require deepeloper/lib-process`.

## Usage

```php
use deepeloper\Lib\Process\Lock;
use deepeloper\Lib\Process\Lock\Storage;

try {
    $lock = new Lock(
        Storage::getLayer("Filesystem", ['path' => "path/to/lock"]),
        60 * 5, // 5 minutes
        true, // Destroy previous lock, false by default
        // "...", // custom lock id
    );
} catch (RuntimeException $e) {
    switch ($e->getCode()) {
        case Lock::EXISTING_IS_VALID:
            // Previous lock is valid, interrupt process.
            die;
        default:
            throw $e;
    }
}

// Long time loop
while (true) {
    // ...
    try {
        $lock->update(true); // true to call \set_time_limit(), false by default
    } catch (RuntimeException $e) {
        // Lock was destroyed by another instance of the daemon
    }
}
```

## Donation
[YooMoney (ex-Yandex.Money), Visa, MasterCard, Maestro](https://yoomoney.ru/to/41001351141494).
