<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;

use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\LibraryDriverInterface;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * DMS library.
 * A library is the root of the document store. It defines where and how to store metadata and content.
 */
class Library extends AbstractLibrary implements LibraryInterface
{
    const CONFIG_LIBRARYSTORAGE = 'library-storage';
    const CONFIG_METASTORAGE = 'meta-storage';
    const CONFIG_DATASTORAGE = 'data-storage';
    private MetaDriverInterface $metaDriver;
    private DataDriverInterface $dataDriver;
    
    
    
    public function __construct(LibraryDriverInterface $libraryDriver)
    {
        $this->libraryDriver = $libraryDriver;
        
        
        $this->metaDriver = new \Ruga\Dms\Driver\Meta\MemoryDriver();
        $this->dataDriver = new \Ruga\Dms\Driver\Data\MemoryDriver();

//        $this->metaAdapter = MetaAdapterFactory::factory($config[self::CONFIG_METASTORAGE] ?? []);
//        $this->dataAdapter = DataAdapterFactory::factory($config[self::CONFIG_DATASTORAGE] ?? []);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createDocument(string $name, DocumentType $documentType): DocumentInterface
    {
        $metaStorage = $this->metaDriver->createStorage();
        $dataStorage = $this->dataDriver->createStorage();
        
        $doc = new Document($this, $metaStorage, $dataStorage);
        $doc->setName($name);
        $doc->setDocumentType($documentType);
        return $doc;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function findDocumentsByUuid($uuid): \ArrayIterator
    {
        $metaStorageContainers = $this->metaDriver->findByUuid($uuid);
        $a = [];
        /** @var MetaStorageContainerInterface $metaStorage */
        foreach ($metaStorageContainers as $metaStorage) {
            $dataStorage = $this->dataDriver->findByMetaStorage($metaStorage);
            $a[] = new Document($this, $metaStorage, $dataStorage);
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function findDocumentsByForeignKey($keys, $categories = null): \ArrayIterator
    {
        throw new \RuntimeException('Not Implemented');
        return new \ArrayIterator();
    }
    
    
}
