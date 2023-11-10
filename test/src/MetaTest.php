<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\Library\JsonFileDriverInterface;
use Ruga\Dms\Driver\Meta\FileDriver;
use Ruga\Dms\Library\Library;
use Ruga\Dms\Library\LibraryInterface;

/**
 * @author                 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class MetaTest extends \Ruga\Dms\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanCreateLibraryInMemory(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('image1', DocumentType::IMAGE());
        
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
        
        echo $document->getCategory();
        echo PHP_EOL;
        
        $this->assertInstanceOf(Document::class, $document);
    }
    
    
    
    public function testCanCreateLibraryInMemoryAndAddContent(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
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
    
    
    
    public function testCanCreateLibraryInMemoryAndAddContentFromFile(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('image2', DocumentType::IMAGE());
        
        $filename = __DIR__ . '/../data/examples/Dinosaur Meme.jpg';
        $document->setContentFromFile($filename);
        $document->save();
        
        echo $document->getFilename();
        echo PHP_EOL;
        
        echo $document->getMimetype();
        echo PHP_EOL;
        $this->assertSame('image/jpeg; charset=binary', $document->getMimetype());
        
        echo $document->getContentLength();
        echo PHP_EOL;
        $this->assertSame(filesize($filename), $document->getContentLength());
        
        echo ($document->getLastModified())->format('c');
        echo PHP_EOL;
    }
    
    
    
    public function testCanCreateLibraryInMemoryAndAddContentFromTemplate(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('config_file', DocumentType::CONFIG());
        
        $filename = __DIR__ . '/../data/examples/config_test_template1.conf';
        $document->setContentFromTemplate($filename, []);
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
    
    
    
    public function testCanCreateLibraryInMemoryAndFindDocByUuid(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('config_file', DocumentType::CONFIG());
        
        $filename = __DIR__ . '/../data/examples/Dinosaur Meme.jpg';
        $document->setContentFromFile($filename);
        $document->save();
        
        echo $document->getUuid();
        echo PHP_EOL;
        $uuid = $document->getUuid();
        
        $doc2 = $library->findDocumentsByUuid($uuid)->current();
        echo $doc2->getDownloadUri();
        echo PHP_EOL;
        $this->assertSame(filesize($filename), $doc2->getContentLength());
    }
    
    
    
    public function testCanCreateLibraryInMemoryAndLinkToKey(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('config_file', DocumentType::CONFIG());
        
        $filename = __DIR__ . '/../data/examples/Dinosaur Meme.jpg';
        $document->setContentFromFile($filename);
        $document->save();
        
        $key = uniqid();
        
        
        
        echo $document->getUuid();
        echo PHP_EOL;
        $uuid = $document->getUuid();
        
        $doc2 = $library->findDocumentsByUuid($uuid)->current();
        echo $doc2->getDownloadUri();
        echo PHP_EOL;
        $this->assertSame(filesize($filename), $doc2->getContentLength());
    }
    
    
    public function testCanCreateLibraryWithFileMeta(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => JsonFileDriverInterface::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1.json',
            ],
            Library::CONFIG_METASTORAGE => [
                'driver' => FileDriver::class,
                'filepath' => __DIR__ . '/../data/libraries/lib1_meta.json',
            ],
        ];
        
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
        $document->setContentFromFile($filename);
        
    }
    
}


