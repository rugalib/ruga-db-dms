<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Row\AbstractRow;
use Ruga\Db\Row\Exception\InvalidColumnException;
use Ruga\Db\Row\Exception\NoDefaultValueException;
use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * Store meta data for a DMS document in a database.
 */
class DbStorageContainer extends AbstractStorageContainer implements MetaStorageContainerInterface
{
    private RowInterface $row;
    
    
    
    public function __construct(MetaDriverInterface $metaDriver, AbstractRow $row)
    {
        $this->row = $row;
        if ($row->isNew()) {
            $row->offsetSet(
                'uuid',
                (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', spl_object_hash($this))))->toString()
            );
        }
        parent::__construct($metaDriver);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->row->offsetGet('name');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->row->offsetSet('name', $name);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getFilename(): ?string
    {
        try {
            return $this->row->offsetGet('filename');
        } catch (InvalidColumnException|NoDefaultValueException $e) {
            return null;
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setFilename(string $name)
    {
        $this->row->offsetSet('filename', $name);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getDocumentType(): DocumentType
    {
        return new DocumentType($this->row->offsetGet('document_type'));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setDocumentType(DocumentType $documentType)
    {
        $this->row->offsetSet('document_type', $documentType->getValue());
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getMimetype(): string
    {
        $mimetype = $this->row->offsetGet('mimetype');
        return empty($mimetype) ? 'application/octet-stream' : $mimetype;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getCategory(): ?string
    {
        return $this->row->offsetGet('category');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setCategory(string $category)
    {
        $this->row->offsetSet('category', $category);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getUuid(): UuidInterface
    {
        return Uuid::fromString($this->row->offsetGet('uuid'));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getLastModified(): \DateTimeImmutable
    {
        return $this->row->offsetGet('lastmodified');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getHash(): ?string
    {
        return $this->row->offsetGet('datahash');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setHash(string $hash)
    {
        $this->row->offsetSet('datahash', $hash);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        $this->row->save();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        $this->row->delete();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setMimetype(string $mimetype)
    {
        $this->row->offsetSet('mimetype', $mimetype);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getDataUniqueKey(): ?string
    {
        return $this->row->offsetGet('datapath');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setDataUniqueKey(string $key)
    {
        $this->row->offsetSet('datapath', $key);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setLastModified(\DateTimeImmutable $lastModified)
    {
        $this->row->offsetSet('lastmodified', $lastModified);
    }
}