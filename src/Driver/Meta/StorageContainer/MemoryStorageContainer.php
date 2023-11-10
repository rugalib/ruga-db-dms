<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

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
            $this->lastmodified = new \DateTimeImmutable();
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getMimetype()
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
    public function getUuid(): string
    {
        $hashedUuid = Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', spl_object_hash($this)));
        return $hashedUuid->toString();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // Not applicable
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        // Not applicatble
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function linkTo(RowInterface $row)
    {
        // TODO: Implement linkTo() method.
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function unlinkFrom(RowInterface $row)
    {
        // TODO: Implement unlinkFrom() method.
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastmodified;
    }
}