<?php

/**
 * PHP-process Lock library.
 *
 * @author [deepeloper](https://github.com/deepeloper)
 * @license [MIT](https://opensource.org/licenses/mit-license.php)
 */

declare(strict_types=1);

namespace deepeloper\Tests\Lib\Process\Lock\StorageLayer;

use deepeloper\Lib\Process\Lock\StorageLayerInterface;

// phpcs:disable

/**
 * Test storage layer.
 */
class Custom implements StorageLayerInterface
{
    public function __construct(array $options)
    {
    }

    public function exists(): bool
    {
    }

    public function delete(): void
    {
    }

    public function get()
    {
    }

    public function getModificationTime(): int
    {
    }

    public function set($data): void
    {
    }

    public function updateModificationTime(?int $time = null): void
    {
    }
}
