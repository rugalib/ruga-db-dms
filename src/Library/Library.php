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
use Ruga\Dms\Driver\LinkStorageContainerInterface;
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
    const CONFIG_LINKSTORAGE = 'link-storage';
    
    
    
    /**
     * @inheritDoc
     */
    public function createDocument(string $name, DocumentType $documentType): DocumentInterface
    {
        $metaStorage = $this->metaDriver->createStorage();
        $dataStorage = $this->dataDriver->createStorage();
        $linkStorage = $this->linkDriver->createStorage();
        
        $doc = new Document($this, $metaStorage, $dataStorage, $linkStorage);
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
        /** @var MetaStorageContainerInterface $metaStorageContainer */
        foreach ($metaStorageContainers as $metaStorageContainer) {
            $a[] = $metaStorageContainer->getDocument();
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function findDocumentsByForeignKey($key, $categories = null): \ArrayIterator
    {
        $linkStorageContainers = $this->linkDriver->findByForeignKey($key);
        $a = [];
        /** @var LinkStorageContainerInterface $linkStorageContainer */
        foreach ($linkStorageContainers as $linkStorageContainer) {
            $a[] = $linkStorageContainer->getDocument();
        }
        return new \ArrayIterator($a);
    }
    
    
}
