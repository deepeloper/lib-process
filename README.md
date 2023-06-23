# PHP-process Lock library
[![Packagist version](https://img.shields.io/packagist/v/deepeloper/lib-fs)](https://packagist.org/packages/deepeloper/lib-process)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/deepeloper/lib-process.svg)](http://php.net/)
[![GitHub license](https://img.shields.io/github/license/deepeloper/lib-process.svg)](https://github.com/deepeloper/lib-process/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues-raw/deepeloper/lib-process.svg)](https://github.com/deepeloper/lib-process/issues)
[![Packagist](https://img.shields.io/packagist/dt/deepeloper/lib-process.svg)](https://packagist.org/packages/deepeloper/lib-process)

[![Donation](https://img.shields.io/badge/Donation-Visa,%20MasterCard,%20Maestro,%20UnionPay,%20YooMoney,%20МИР-red)](https://yoomoney.ru/to/41001351141494)

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
