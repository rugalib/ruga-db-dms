<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\MetaUuid;

class MemoryStorageContainer extends AbstractStorageContainer implements MetaStorageContainerInterface
{
    private string $name;
    private string $filename;
    private DocumentType $documentType;
    private string $category;
    private string $mimetype;
    private \DateTimeImmutable $lastmodified;
    private string $hash;
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getFilename(): ?string
    {
        return $this->filename ?? null;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getDataUniqueKey(): ?string
    {
        return $this->getDocument()->getDataStorageContainer()->getUuid();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setDataUniqueKey(string $key)
    {
        // not applicable
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getDocumentType(): DocumentType
    {
        return $this->documentType;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setDocumentType(DocumentType $documentType)
    {
        $this->documentType = $documentType;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getHash(): ?string
    {
        return $this->hash ?? null;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setHash(string $hash)
    {
        if (($this->hash ?? '') != $hash) {
            $this->hash = $hash;
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getMimetype(): string
    {
        return $this->mimetype ?? 'application/octet-stream';
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setMimetype(string $mimetype)
    {
        $this->mimetype = $mimetype;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getCategory(): ?string
    {
        return $this->category ?? null;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getUuid(): UuidInterface
    {
        return Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', spl_object_hash($this)));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // MemoryStorageContainer has no save function
        // calling parent driver's save()
        $this->getMetaDriver()->save();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        // Not applicatble
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastmodified;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setLastModified(\DateTimeImmutable $lastModified)
    {
        $this->lastmodified = $lastModified;
    }
}