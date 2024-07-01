<?php

/**
 * PHP-process Lock library.
 *
 * @author [deepeloper](https://github.com/deepeloper)
 * @license [MIT](https://opensource.org/licenses/mit-license.php)
 */

declare(strict_types=1);

namespace deepeloper\Lib\Process\Lock;

use deepeloper\Lib\Process\Lock\StorageLayer\Filesystem;
use Error;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Storage layer factory tests.
 */
class StorageTest extends TestCase
{
    /**
     * Tests exception when invalid layer.
     */
    public function testExceptionWhenInvalidLayer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/^Invalid layer/");
        Storage::getLayer("../Layer", []);
    }

    /**
     * Tests common layer.
     */
    public function testCommonLayer(): void
    {
        $layer = Storage::getLayer("Filesystem", ['path' => "..."]);
        self::assertEquals(Filesystem::class, \get_class($layer));
    }

    /**
     * Tests unknown layer.
     */
    public function testUnknownLayer(): void
    {
        $this->expectException(Error::class);
        Storage::getLayer("Unknown");
    }

    /**
     * Tests custom layer.
     */
    public function testCustomLayer(): void
    {
        $layer = Storage::getLayer("\\deepeloper\\Tests\\Lib\\Process\\Lock\\StorageLayer\Custom", []);
        self::assertTrue($layer instanceof StorageLayerInterface);
    }
}
