<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Psr\Container\ContainerExceptionInterface;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Document\DocumentType;
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
     * @return void
     * @throws ContainerExceptionInterface
     * @dataProvider libraryConfigProvider
     */
    public function testCanCreateAndReloadLibrary(array $config): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo "saving library {$library->getName()}";
        echo PHP_EOL;
        $library->save();
        $libraryConfig = $library->dumpConfig();
        
        // Re-Load the library into $lib2
        /** @var LibraryInterface $lib2 */
        $lib2 = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo "loded library {$lib2->getName()}";
        echo PHP_EOL;
        
        
        // Add documents
        $doc1 = $lib2->createDocument('config test template 1', DocumentType::CONFIG());
        $doc1->setFilename('configTestFile1.conf');
        $doc1->setContentFromTemplate(__DIR__ . '/../data/examples/config_test_template1.conf');
        $doc1->linkTo('link1');
        $doc1->save();
        
        $doc2 = $lib2->createDocument('Dino', DocumentType::IMAGE());
        $doc2->setContentFromFile(__DIR__ . '/../data/examples/Dinosaur Meme.jpg');
        $doc2->linkTo('link1');
        $doc2->linkTo('link2');
        $doc2->linkTo('link3');
        $doc2->save();
        
        
        // Re-Load the library into $lib3
        /** @var LibraryInterface $lib3 */
        $lib3 = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        echo "loded library {$lib3->getName()}";
        echo PHP_EOL;
        
        // Find documents by link
        $docs = $lib3->findDocumentsByForeignKey('link1');
        if ($libraryConfig[Library::CONFIG_LINKSTORAGE]['driver'] == \Ruga\Dms\Driver\Link\MemoryDriver::class) {
            // The below tests do not make sense, if link driver is only in memory
            return;
        }
        /** @var DocumentInterface $doc */
        foreach ($docs as $doc) {
            echo "found document {$doc->getName()}";
            echo PHP_EOL;
        }
        
        // remove links
        /** @var DocumentInterface $doc3 */
        $doc3 = $lib3->findDocumentsByForeignKey('link3')->current();
        $this->assertInstanceOf(DocumentInterface::class, $doc3);
        $doc3->unlinkFrom('link3');
        $doc3->save();
        $docs = $lib3->findDocumentsByForeignKey('link3');
        $this->assertCount(0, $docs);
        echo "unlinked document {$doc3->getName()} from link3";
        echo PHP_EOL;
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
                    Library::CONFIG_METASTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Meta\DbDriver::class,
                        'adapter' => $this->getAdapter(),
                    ],
                    Library::CONFIG_DATASTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Data\ObjectstorageDriver::class,
                        'basepath' => __DIR__ . '/../data/files/',
                    ],
                    Library::CONFIG_LINKSTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Link\DbDriver::class,
                        'adapter' => $this->getAdapter(),
                    ],
                ],
            ],
        
        ];
    }
    
}


