<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Psr\Container\ContainerExceptionInterface;
use Ruga\Dms\Driver\Library\DbDriver;
use Ruga\Dms\Driver\Library\JsonFileDriver;
use Ruga\Dms\Library\Library;
use Ruga\Dms\Library\LibraryInterface;

/**
 * @author                 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class LibraryTest extends \Ruga\Dms\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanCreateLibraryInMemory(): void
    {
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
    }
    
    
    
    public function testCanCreateLibraryCustomizedFile(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => JsonFileDriver::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1.json',
            ],
//            Library::CONFIG_METASTORAGE => [
//                'driver' => 'db',
//                'adapter' => $this->getAdapter(),
//                'table' => \Ruga\Dms\Adapter\Meta\Db\DocumentTable::class,
//            ],
//            Library::CONFIG_DATASTORAGE => [
//                'driver' => 'filesystem',
//                'path' => __DIR__ . '/data/files',
//            ],
        ];
        
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
    }
    
    
    
    public function testCanCreateLibraryCustomizedDb(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => DbDriver::class,
            ],
//            Library::CONFIG_METASTORAGE => [
//                'driver' => 'db',
//                'adapter' => $this->getAdapter(),
//                'table' => \Ruga\Dms\Adapter\Meta\Db\DocumentTable::class,
//            ],
//            Library::CONFIG_DATASTORAGE => [
//                'driver' => 'filesystem',
//                'path' => __DIR__ . '/data/files',
//            ],
        ];
        
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
    }
    
    
    
    /**
     * @param array $config
     *
     * @return LibraryInterface
     * @dataProvider libraryConfigProvider
     * @throws ContainerExceptionInterface
     */
    public function testCanCreateLibrary(array $config): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
//        $newName = uniqid($library->getName(), true);
//        $library->setName($newName);
        $library->save();
        
        
        // Re-Load the library into $lib2
        /** @var LibraryInterface $lib2 */
        $lib2 = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo $lib2->getName();
        echo PHP_EOL;
//        $this->assertSame($newName, $lib2->getName());
    }
    
    
    
    public function libraryConfigProvider(): array
    {
        return [
            'MemoryDriver' => [
                [
                    'name' => 'MemoryDriver Library',
                    Library::CONFIG_LIBRARYSTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Library\MemoryDriver::class,
                    ],
                ],
            ],
            'JsonFileDriver' => [
                [
                    'name' => 'JsonFileDriver Library',
                    Library::CONFIG_LIBRARYSTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Library\JsonFileDriver::class,
                        'filepath' => __DIR__ . '/../data/libraries/JsonFileDriver.json',
                    ],
                ],
            ],
            'DbDriver' => [
                [
                    'name' => 'DbDriver Library',
                    Library::CONFIG_LIBRARYSTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Library\DbDriver::class,
                    ],
                ],
            ],
        
        ];
    }
    
}


