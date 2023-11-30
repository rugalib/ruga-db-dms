<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use PhpParser\Comment\Doc;
use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\Data\ObjectstorageDriver;
use Ruga\Dms\Driver\Library\DbDriverInterface;
use Ruga\Dms\Driver\Library\JsonFileDriverInterface;
use Ruga\Dms\Driver\Meta\DbDriver;
use Ruga\Dms\Library\Library;
use Ruga\Dms\Library\LibraryInterface;

/**
 * @author                 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class LinkTest extends \Ruga\Dms\Test\PHPUnit\AbstractTestSetUp
{
    
    
    public function testCanCreateLibraryInMemoryAndLinkToObject(): void
    {
        /** @var LibraryInterface $library */
        $library = $this->getContainer()->get(LibraryInterface::class);
        $this->assertInstanceOf(Library::class, $library);
        echo $library->getName();
        echo PHP_EOL;
        
        $document = $library->createDocument('image2', DocumentType::IMAGE());
        
        $filename = __DIR__ . '/../data/examples/Dinosaur Meme.jpg';
        $document->setContentFromFile($filename);
        
        $document->linkTo('hallowelt');
        $document->save();
        
        
        $doc2 = $library->findDocumentsByForeignKey('hallowelt')->current();
        
        
        echo $doc2->getFilename();
        echo PHP_EOL;
        
        echo $doc2->getMimetype();
        echo PHP_EOL;
        $this->assertSame('image/jpeg; charset=binary', $doc2->getMimetype());
        
        echo $doc2->getContentLength();
        echo PHP_EOL;
        $this->assertSame(filesize($filename), $doc2->getContentLength());
        
        echo ($doc2->getLastModified())->format('c');
        echo PHP_EOL;
    }
    
    
    
    public function testCanCreateLibraryAndLinkWithDbDriver(): void
    {
        $config = [
            'name' => 'Customized Library',
            Library::CONFIG_LIBRARYSTORAGE => [
                'driver' => DbDriverInterface::class,
                'adapter' => $this->getAdapter(),
            ],
            Library::CONFIG_METASTORAGE => [
                'driver' => DbDriver::class,
                'adapter' => $this->getAdapter(),
            ],
            Library::CONFIG_DATASTORAGE => [
                'driver' => ObjectstorageDriver::class,
                'basepath' => __DIR__ . '/../data/files/',
            ],
            Library::CONFIG_LINKSTORAGE => [
                'driver' => \Ruga\Dms\Driver\Link\DbDriver::class,
                'adapter' => $this->getAdapter(),
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
        $document->linkTo('6@BillTable');
        $document->setContentFromFile($filename);
        
        $document = $library->createDocument('image 3', DocumentType::IMAGE());
        $filename = __DIR__ . '/../data/examples/shutdown-command.gif';
        $document->setFilename('hello/world/shutdown-command.gif');
        $document->setContentFromFile($filename);
        
        $document->linkTo('7@BillTable');
        $document->linkTo('8@BillTable');
        $document->linkTo('9@BillTable');
        $document->save();
        $uuid = $document->getUuid();
        
        
        /** @var Document $doc2 */
        $doc2 = $library->findDocumentsByUuid($uuid)->current();
        echo $doc2->getFilename() . ' ';
        echo $doc2->getMimetype();
        echo PHP_EOL;
        $this->assertSame('image/gif; charset=binary', $doc2->getMimetype());
        
        
        /** @var Document $doc3 */
        $doc3 = $library->findDocumentsByForeignKey('6@BillTable')->current();
        echo $doc3->getFilename() . ' ';
        echo $doc3->getMimetype();
        echo PHP_EOL;
        $this->assertSame('image/png; charset=binary', $doc3->getMimetype());
    }
    
    
}


