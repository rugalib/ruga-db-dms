<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Ruga\Dms\Driver\Library\DbDriverInterface;
use Ruga\Dms\Driver\Library\JsonFileDriverInterface;
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
                'driver' => JsonFileDriverInterface::class,
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
                'driver' => DbDriverInterface::class,
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
    
    
}


