<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ruga\Dms\Driver\DataStorageContainerInterface;

class MemoryStorageContainer extends AbstractStorageContainer implements DataStorageContainerInterface
{
    private string $content;
    
    
    
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
    public function getContent(): string
    {
        return $this->content;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setContent($data): bool
    {
        $metaStorage = $this->getDocument()->getMetaStorageContainer();
        
        $newhash = $metaStorage->calculateHash($data);
        
        // Hash changed => persist new content to the data backend
        if ($newhash != $metaStorage->getHash()) {
            $this->content = $data;
            $metaStorage->setHash($newhash);
            return true;
        }
        
        return false;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
        // Not applicable
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        unset($this->content);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getContentLength(): int
    {
        return strlen($this->content);
    }
}