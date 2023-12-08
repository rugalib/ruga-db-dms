<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * This abstract storage container implements all common methods for the concrete storage containers.
 *
 * @see \Ruga\Dms\Driver\Meta\StorageContainer\DbStorageContainer
 * @see \Ruga\Dms\Driver\Meta\StorageContainer\MemoryStorageContainer
 */
abstract class AbstractStorageContainer implements MetaStorageContainerInterface
{
    private DocumentInterface $document;
    private MetaDriverInterface $metaDriver;
    
    
    
    public function __construct(MetaDriverInterface $metaDriver)
    {
        $this->metaDriver = $metaDriver;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setDocument(DocumentInterface $document)
    {
        $this->document = $document;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getDocument(): ?DocumentInterface
    {
        return $this->document ?? null;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getMetaDriver(): MetaDriverInterface
    {
        return $this->metaDriver;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function calculateHash(string $data): string
    {
        return hash('sha256', $data);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function calcualteFileHash(string $filename): string
    {
        return hash_file('sha256', $filename);
    }
    
    
    
    public function toArray(): array
    {
        $a = [];
        $a['name'] = $this->getName();
        $a['uuid'] = $this->getUuid();
        $a['documentuuid'] = $this->getDocument()->getUuid();
        $a['datauuid'] = $this->getDocument()->getDataStorageContainer()->getUuid();
        $a['filename'] = $this->getFilename();
        $a['documenttype'] = $this->getDocumentType()->getValue();
        $a['mimetype'] = $this->getMimetype();
        $a['lastmodified'] = $this->getLastModified()->format('c');
        $a['priority'] = 0;
        $a['datapath'] = $this->getDataUniqueKey();
        $a['datahash'] = $this->getHash();
        return $a;
    }
    
}