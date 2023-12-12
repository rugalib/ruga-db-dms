<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Link\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ruga\Db\Row\AbstractRugaRow;
use Ruga\Dms\Driver\Link\LinkObject;
use Ruga\Dms\Driver\LinkStorageContainerInterface;

/**
 * Store links for a DMS document in memory.
 */
class MemoryStorageContainer extends AbstractStorageContainer implements LinkStorageContainerInterface
{
    
    
    /**
     * @inheritDoc
     */
    public function linkTo($key)
    {
        $key = $this->keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        $o = new LinkObject($key, $keyUuid, $this->getDocument()->getUuid()->toString());
        $this->links->attach($o);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function unlinkFrom($key)
    {
        $key = $this->keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            if ($link->foreignUuid === $keyUuid) {
                $this->links->offsetUnset($link);
                break;
            }
        }
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function isLinkedTo($key): bool
    {
        $key = $this->keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            if ($link->foreignUuid === $keyUuid) {
                return true;
            }
        }
        return false;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // MemoryStorageContainer has no save function
        // calling parent driver's save()
        $this->getLinkDriver()->save();
    }
    
    
    
    /**
     * @inheritdoc
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
        $this->links->removeAll($this->links);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getLinks(): \ArrayIterator
    {
        $a = [];
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            $a[$link->foreignUuid] = $link;
        }
        
        return new \ArrayIterator($a);
    }
}