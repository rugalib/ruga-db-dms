<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Link\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Row\AbstractRugaRow;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;

abstract class AbstractStorageContainer implements LinkStorageContainerInterface
{
    private DocumentInterface $document;
    protected \SplObjectStorage $links;
    private LinkDriverInterface $linkDriver;
    private UuidInterface $metaUuid;
    
    
    
    public function __construct(LinkDriverInterface $linkDriver, UuidInterface $metaUuid)
    {
        $this->linkDriver = $linkDriver;
        $this->metaUuid = $metaUuid;
        $this->links = new \SplObjectStorage();
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
    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getLinkDriver(): LinkDriverInterface
    {
        return $this->linkDriver;
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
    public function getMetaUuid(): UuidInterface
    {
        return $this->metaUuid;
    }
    
    
    
    /**
     * Extracts a unique key from an object.
     *
     * @param $key
     *
     * @return string
     */
    protected function keyFromMixed($key): string
    {
        if (is_scalar($key)) {
            return strval($key);
        }
        
        if (is_array($key)) {
            throw new \InvalidArgumentException("Array is not supported as key");
        }
        
        if (is_object($key)) {
            if ($key instanceof AbstractRugaRow) {
                return strval($key->uniqueid);
            }
            return strval($key);
        }
        
        throw new \InvalidArgumentException(gettype($key) . " is not supported as key");
    }
    
    
}