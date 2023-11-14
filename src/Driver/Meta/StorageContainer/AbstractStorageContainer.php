<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ruga\Dms\Document\Document;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

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
    public function calculateHash(string $data)
    {
        return hash('sha256', $data);
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