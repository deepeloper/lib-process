<?php

/**
 * PHP-process Lock library.
 *
 * @author [deepeloper](https://github.com/deepeloper)
 * @license [MIT](https://opensource.org/licenses/mit-license.php)
 */

declare(strict_types=1);

namespace deepeloper\Lib\Process\Lock\StorageLayer;

use deepeloper\Lib\Process\LockTestCase;
use InvalidArgumentException;
use RuntimeException;

/**
 * Filesystem storage layer tests.
 *
 * @coversDefaultClass \deepeloper\Lib\Process\Lock\StorageLayer\Filesystem
 */
class FilesystemTest extends LockTestCase
{
    /**
     * Tests exception when required 'path' option missing.
     */
    public function testExceptionWhenMissingPathOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("\$options['path'] required");
        new Filesystem([]);
    }

    /**
     * Tests exception when 'path' option isn't a string.
     */
    public function testExceptionWhenInvalodPathOption(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("\$options['path'] must be a string");
        new Filesystem(['path' => []]);
    }

    /**
     * Tests exception when 'path' deleting invalid record.
     */
    public function testExceptionWhenDeletingInvalidRecord(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot delete data from '{$this->path}'");
        $storage = new Filesystem(['path' => $this->path]);
        $storage->delete();
    }

    /**
     * Tests exception when 'path' getting invalid record.
     */
    public function testExceptionWhenGettingInvalidRecord(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot get record data from '{$this->path}'");
        $storage = new Filesystem(['path' => $this->path]);
        $storage->get();
    }

    /**
     * Tests exception when 'path' getting invalid record modification time.
     */
    public function testExceptionWhenGettingInvalidRecordModificationTime(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot get record modification time from '{$this->path}'");
        $storage = new Filesystem(['path' => $this->path]);
        $storage->getModificationTime();
    }

    /**
     * Tests exception when 'path' setting invalid record.
     */
    public function testExceptionWhenSettingInvalidRecord(): void
    {
        $path = "/invalid/path";
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot set record data to '{$path}'");
        $storage = new Filesystem(['path' => $path]);
        $storage->set('test');
    }

    /**
     * Tests exception when 'path' updating invalid record time.
     */
    public function testExceptionWhenUpdatingInvalidRecordTime(): void
    {
        $path = "/invalid/path";
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cannot update modification time of record at '{$path}'");
        $storage = new Filesystem(['path' => $path]);
        $storage->updateModificationTime();
    }

    /**
     * Tests common functionality.
     */
    public function testCommonFunctionality(): void
    {
        $storage = new Filesystem(['path' => $this->path]);
        self::assertFalse($storage->exists());
        $time = \time();
        $storage->set('test');
        self::assertEquals('test', $storage->get());
        self::assertTrue($storage->exists());
        self::assertEquals($time, $storage->getModificationTime());
        $time += 100500;
        $storage->updateModificationTime();
        self::assertNotEquals($time, $storage->getModificationTime());
        $storage->updateModificationTime($time);
        self::assertEquals($time, $storage->getModificationTime());
        $storage->delete();
        self::assertFalse($storage->exists());
    }
}
