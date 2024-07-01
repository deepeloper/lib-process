<?php

/**
 * PHP-process Lock library.
 *
 * @author [deepeloper](https://github.com/deepeloper)
 * @license [MIT](https://opensource.org/licenses/mit-license.php)
 */

declare(strict_types=1);

namespace deepeloper\Lib\Process;

use deepeloper\Lib\Process\Lock\Storage;
use deepeloper\Lib\Process\Lock\StorageLayer\Filesystem;
use deepeloper\Lib\Process\Lock\StorageLayerInterface;
use RuntimeException;

/**
 * Lock tests.
 */
class LockTest extends LockTestCase
{
    protected StorageLayerInterface $storage;
    protected Lock $lock;

    /**
     * Tests exception when existing is valid.
     */
    public function testExceptionWhenExistingIsValid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::EXISTING_IS_VALID);
        $this->expectExceptionMessage("Previous lock is still valid");
        new Lock($this->storage, 1);
    }

    /**
     * Tests exception when already exists.
     */
    public function testExceptionWhenExists(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::EXISTS);
        $this->expectExceptionMessage("Lock already exists");
        $time = $this->storage->getModificationTime();
        $time -= 60 * 10; // 10 minutes ago
        $this->storage->updateModificationTime($time);
        new Lock($this->storage, 1);
    }

    /**
     * Tests exception when cannot destroy previous.
     */
    public function testExceptionWhenCannotDestroyPrevious(): void
    {
        $this->expectException(RuntimeException::class);
        // $this->expectExceptionCode(Lock::CANNOT_DESTROY_PREVIOUS);
        $this->expectExceptionMessageMatches("/^Cannot destroy previous lock:/");
        $time = $this->storage->getModificationTime();
        $time -= 60 * 10; // 10 minutes ago
        $this->storage->updateModificationTime($time);
        $stub = $this->getMockBuilder(Filesystem::class)
            ->setMethodsExcept(\array_diff(\get_class_methods(Filesystem::class), ['delete']))
            ->setConstructorArgs([['path' => $this->path]])
            ->getMock();
        $stub->method('delete')->will($this->throwException(new RuntimeException()));
        new Lock($stub, 1, true);
    }

    /**
     * Tests exception when cannot create.
     */
    public function testExceptionWhenCannotCreate(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::CANNOT_CREATE);
        $this->expectExceptionMessageMatches("/^Cannot create lock:/");
        $time = $this->storage->getModificationTime();
        $time -= 60 * 10; // 10 minutes ago
        $this->storage->updateModificationTime($time);
        $stub = $this->getMockBuilder(Filesystem::class)
            ->setMethodsExcept(\array_diff(\get_class_methods(Filesystem::class), ['set']))
            ->setConstructorArgs([['path' => $this->path]])
            ->getMock();
        $stub->method('set')->will($this->throwException(new RuntimeException()));
        new Lock($stub, 1, true);
    }

    /**
     * Tests exception when lock destroyed.
     */
    public function testExceptionWhenLockDestroyed(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::DESTROYED);
        $this->expectExceptionMessage("Lock destroyed");
        $this->storage->delete();
        $this->lock->validate();
    }

    /**
     * Tests exception when wrong process Id.
     */
    public function testExceptionWhenWrongProcessId(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::WRONG_PROCESS_ID);
        $this->expectExceptionMessageMatches("/^Lock contains wrong process Id 'wrong' instead of/");
        $this->storage->set("wrong");
        $this->lock->validate();
    }

    /**
     * Tests exception when cannot update.
     */
    public function testExceptionWhenCannotUpdate(): void
    {
        unset($this->lock);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::CANNOT_UPDATE);
        $this->expectExceptionMessageMatches("/^Cannot update lock: /");
        $stub = $this->getMockBuilder(Lock::class)
            ->setMethodsExcept(\array_diff(\get_class_methods(Lock::class), ['validate']))
            ->setConstructorArgs([$this->storage, 1])
            ->getMock();
        $processId = $this->storage->get();
        $this->storage->delete();
        try {
            $stub->update();
        } catch (RuntimeException $e) {
            $this->storage->set($processId);
            throw $e;
        }
    }

    /**
     * Tests exception when cannot delete in destructor.
     */
    public function testExceptionWhenCannotDelete(): void
    {
        unset($this->lock);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(Lock::CANNOT_DELETE);
        $this->expectExceptionMessageMatches("/^Cannot delete lock: /");
        $stub = $this->getMockBuilder(Filesystem::class)
            ->setMethodsExcept(\array_diff(\get_class_methods(Filesystem::class), ['delete']))
            ->setConstructorArgs([['path' => $this->path]])
            ->getMock();
        $stub->method('delete')->will($this->throwException(new RuntimeException()));
        new Lock($stub, 1);
    }

    /**
     * Tests common functionality.
     */
    public function testCommonFunctionality(): void
    {
        self::assertMatchesRegularExpression("/^\d+\.\d+(.\d+)?$/", $this->storage->get());
        self::assertNull($this->lock->validate());
        self::assertNull($this->lock->update());
        unset($this->lock);
        $ttl = 1;
        $storage = Storage::getLayer("Filesystem", ['path' => $this->path]);
        $lock = new Lock($storage, $ttl, false, "test");
        self::assertEquals("test", $storage->get());
        unset($lock);
        self::assertFalse($storage->exists());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::getLayer("Filesystem", ['path' => $this->path]);
        $this->lock = new Lock($this->storage, 1);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        try {
            unset($this->lock);
        } catch (RuntimeException $e) {
        }

        parent::tearDown();
    }
}
