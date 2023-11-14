<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Link\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Table\AbstractTable;
use Ruga\Dms\Driver\Link\LinkObject;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;
use Ruga\Dms\Model\Link;

class DbStorageContainer extends AbstractStorageContainer implements LinkStorageContainerInterface
{
    private AbstractTable $table;
    
    
    
    public function __construct(LinkDriverInterface $linkDriver, UuidInterface $metaUuid, AbstractTable $table)
    {
        parent::__construct($linkDriver, $metaUuid);
        $this->table = $table;
    }
    
    
    
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
     * @inheritDoc
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
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            /** @var Link $row */
            if (!$row = $this->table->select(['Foreign_uuid' => $link->foreignUuid, 'Meta_uuid' => $link->metaUuid]
            )->current()) {
                /** @var Link $row */
                $row = $this->table->createRow();
                $row->offsetSet('Foreign_uuid', $link->foreignUuid);
                $row->offsetSet('Meta_uuid', $link->metaUuid);
            }
            $row->offsetSet('Foreign_key', $link->foreignKey);
            $row->offsetSet('remark', $link->remark);
            $row->save();
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
        // TODO: Implement rename() method.
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        $this->links->removeAll($this->links);
    }
    
    
    
    public function findByForeignKey($key): \ArrayIterator
    {
        $key = $this->keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        $a = [];
        /** @var Link $row */
        foreach ($this->table->select(['Foreign_uuid' => $keyUuid]) as $row) {
            $a[] = $this->getLinkDriver()->createStorage();
        }
        return new \ArrayIterator($a);
    }
    
}