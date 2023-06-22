<?php

/**
 * PHP-process Lock library.
 *
 * @author [deepeloper](https://github.com/deepeloper)
 * @license [MIT](https://opensource.org/licenses/mit-license.php)
 */

declare(strict_types=1);

namespace deepeloper\Lib\Process\Lock;

// phpcs:disable Squiz.Commenting.FunctionComment.Missing

/**
 * Storage layer interface.
 */
interface StorageLayerInterface
{
    public function __construct(array $options);

    public function delete(): void;

    public function exists(): bool;

    /**
     * @return mixed
     */
    public function get();

    public function getModificationTime(): int;

    /**
     * @param mixed $data
     */
    public function set($data): void;

    /**
     * @param int|null $time \time() by default
     */
    public function updateModificationTime(?int $time = null): void;
}
