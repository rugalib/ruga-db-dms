<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentType;
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
    
    
}


