<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\Data\FilesystemDriver;
use Ruga\Dms\Driver\Library\JsonFileDriverInterface;
use Ruga\Dms\Driver\Meta\DbDriver;
use Ruga\Dms\Driver\Meta\FileDriver;
use Ruga\Dms\Library\Library;
use Ruga\Dms\Library\LibraryInterface;
use Ruga\Dms\Model\DocumentTable;

/**
 * @author                 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class DataTest extends \Ruga\Dms\Test\PHPUnit\AbstractTestSetUp
{
    
    public function testCanCreateLibraryWithFilesystemDriver(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => JsonFileDriverInterface::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1.json',
            ],
            Library::CONFIG_METASTORAGE => [
                'driver' => DbDriver::class,
                'adapter' => $this->getAdapter(),
            ],
            Library::CONFIG_DATASTORAGE => [
                'driver' => FilesystemDriver::class,
                'basepath' => __DIR__ . '/../data/files/',
            ],
        ];
        
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        
        $document = $library->createDocument('image 1', DocumentType::IMAGE());
        $filename = __DIR__ . '/../data/examples/Dinosaur Meme.jpg';
        $document->setContentFromFile($filename);
        
        $document = $library->createDocument('image 2', DocumentType::IMAGE());
        $filename = __DIR__ . '/../data/examples/2b or !2b.png';
        $document->setContentFromFile($filename);
        
        $document = $library->createDocument('image 3', DocumentType::IMAGE());
        $filename = __DIR__ . '/../data/examples/shutdown-command.gif';
        $document->setFilename('hello/world/shutdown-command.gif');
        $document->setContentFromFile($filename);
    }
    
    
    
    public function testCanCreateLibraryWithFilesystemDriverAndAddContent(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => JsonFileDriverInterface::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1.json',
            ],
            Library::CONFIG_METASTORAGE => [
                'driver' => DbDriver::class,
                'adapter' => $this->getAdapter(),
            ],
            Library::CONFIG_DATASTORAGE => [
                'driver' => FilesystemDriver::class,
                'basepath' => __DIR__ . '/../data/files/',
            ],
        ];
        
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('text', DocumentType::CONFIG());
        
        $str = <<< EOT
#!/bin/bash
#
# Hallo Welt

EOT;
        
        
        $document->setContent($str);
        
        echo $document->getName();
        echo PHP_EOL;
        
        echo $document->getDocumentType();
        echo PHP_EOL;
        
        echo $document->getUuid();
        echo PHP_EOL;
        
        echo $document->getFilename();
        echo PHP_EOL;
        
        echo $document->getMimetype();
        echo PHP_EOL;
        $this->assertSame('text/x-shellscript; charset=us-ascii', $document->getMimetype());
        
        echo $document->getCategory();
        echo PHP_EOL;
        
        echo $document->getContent();
        echo PHP_EOL;
        
        echo $document->getContentLength();
        echo PHP_EOL;
        $this->assertSame(strlen($str), $document->getContentLength());
    }
    
    
    
    public function testCanCreateLibraryWithFilesystemDriverAndAddContentFromTemplate(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => JsonFileDriverInterface::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1.json',
            ],
            Library::CONFIG_METASTORAGE => [
                'driver' => DbDriver::class,
                'adapter' => $this->getAdapter(),
            ],
            Library::CONFIG_DATASTORAGE => [
                'driver' => FilesystemDriver::class,
                'basepath' => __DIR__ . '/../data/files/',
            ],
        ];
        
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->build(LibraryInterface::class, $config);
        $this->assertInstanceOf(Library::class, $library);
        
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('config_file', DocumentType::CONFIG());
        
        $filename = __DIR__ . '/../data/examples/config_test_template1.conf';
        $document->setContentFromTemplate($filename, []);
        $document->setFilename('hello/world/config.conf');
        $document->save();
        
        echo $document->getFilename();
        echo PHP_EOL;
        
        echo $document->getMimetype();
        echo PHP_EOL;
        $this->assertSame('text/plain; charset=us-ascii', $document->getMimetype());
        
        echo $document->getContent();
        echo PHP_EOL;
        
        echo ($document->getLastModified())->format('c');
        echo PHP_EOL;
        
        $this->assertSame(99, $document->getContentLength());
        
        echo $document->getDownloadUri();
        echo PHP_EOL;
    }
    
    
}


