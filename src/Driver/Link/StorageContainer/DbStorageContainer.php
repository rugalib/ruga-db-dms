<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Link\StorageContainer;

use Laminas\Db\Sql\Where;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Table\AbstractTable;
use Ruga\Dms\Driver\Link\LinkObject;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;
use Ruga\Dms\Model\Link;

/**
 * Store links for a DMS document in a database.
 */
class DbStorageContainer extends AbstractStorageContainer implements LinkStorageContainerInterface
{
    private AbstractTable $table;
    protected \SplObjectStorage $linksToRemove;
    
    
    
    public function __construct(LinkDriverInterface $linkDriver, UuidInterface $metaUuid, AbstractTable $table)
    {
        parent::__construct($linkDriver, $metaUuid);
        $this->linksToRemove = new \SplObjectStorage();
        $this->table = $table;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function linkTo($key)
    {
        $key = static::keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        $o = new LinkObject($key, $keyUuid, $this->getDocument()->getUuid()->toString());
        $this->links->attach($o);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function unlinkFrom($key)
    {
        $key = static::keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            if (!empty($link->foreignUuid) && ($link->foreignUuid === $keyUuid)) {
                $this->linksToRemove->attach($this->links->offsetGet($link));
                $this->links->offsetUnset($link);
                return;
            }
            if (empty($link->foreignUuid) && ($link->foreignkey === $key)) {
                $this->linksToRemove->attach($this->links->offsetGet($link));
                $this->links->offsetUnset($link);
                return;
            }
        }
        // LinkObject not found in link list => create a new one
        $o = new LinkObject($key, $keyUuid, $this->getDocument()->getUuid()->toString());
        $this->linksToRemove->attach($o);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function isLinkedTo($key): bool
    {
        $key = static::keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            if (!empty($link->foreignUuid) && ($link->foreignUuid === $keyUuid)) {
                return true;
            }
            if (empty($link->foreignUuid) && ($link->foreignkey === $key)) {
                return true;
            }
        }
        
        
        $link = new LinkObject($key, $keyUuid, $this->getDocument()->getUuid()->toString());
        $select = $this->table->getSql()->select();
        $select->where(function (Where $where) use ($link) {
            $where
                ->NEST
                ->NEST->isNotNull('Foreign_uuid')->AND->equalTo('Foreign_uuid', $link->foreignUuid)->UNNEST
                ->OR
                ->NEST->isNull('Foreign_uuid')->AND->equalTo('Foreign_key', $link->foreignKey)->UNNEST
                ->UNNEST;
            $where->equalTo('Meta_uuid', $link->metaUuid);
        });
//            \Ruga\Log::addLog("SQL={$select->getSqlString($this->table->getAdapter()->getPlatform())}");
        
        /** @var Link $row */
        if ($this->table->selectWith($select)->current()) {
            return true;
        }
        
        return false;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // Save links
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            $select = $this->table->getSql()->select();
            $select->where(function (Where $where) use ($link) {
                $where
                    ->NEST
                    ->NEST->isNotNull('Foreign_uuid')->AND->equalTo('Foreign_uuid', $link->foreignUuid)->UNNEST
                    ->OR
                    ->NEST->isNull('Foreign_uuid')->AND->equalTo('Foreign_key', $link->foreignKey)->UNNEST
                    ->UNNEST;
                $where->equalTo('Meta_uuid', $link->metaUuid);
            });
//            \Ruga\Log::addLog("SQL={$select->getSqlString($this->table->getAdapter()->getPlatform())}");
            
            /** @var Link $row */
            if (!$row = $this->table->selectWith($select)->current()) {
                $row = $this->table->createRow();
                $row->offsetSet('Meta_uuid', $link->metaUuid);
            }
            $row->offsetSet('Foreign_uuid', $link->foreignUuid);
            $row->offsetSet('Foreign_key', $link->foreignKey);
            $row->offsetSet('remark', $link->remark);
            $row->save();
        }
        
        // Remove links in linksToRemove
        /** @var LinkObject $link */
        foreach ($this->linksToRemove as $link) {
            $select = $this->table->getSql()->select();
            $select->where(function (Where $where) use ($link) {
                $where
                    ->NEST
                    ->NEST->isNotNull('Foreign_uuid')->AND->equalTo('Foreign_uuid', $link->foreignUuid)->UNNEST
                    ->OR
                    ->NEST->isNull('Foreign_uuid')->AND->equalTo('Foreign_key', $link->foreignKey)->UNNEST
                    ->UNNEST;
                $where->equalTo('Meta_uuid', $link->metaUuid);
            });
//            \Ruga\Log::addLog("SQL={$select->getSqlString($this->table->getAdapter()->getPlatform())}");
            
            /** @var Link $row */
            if ($row = $this->table->selectWith($select)->current()) {
                $row->delete();
            }
        }
        $this->linksToRemove->removeAllExcept(new \SplObjectStorage());
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
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
        
        $metaUuid = $this->getDocument()->getUuid()->toString();
        $select = $this->table->getSql()->select();
        $select->where(function (Where $where) use ($metaUuid) {
            $where->equalTo('Meta_uuid', $metaUuid);
        });
        
        /** @var Link $row */
        foreach ($this->table->selectWith($select) as $row) {
            $o = new LinkObject($row->offsetGet('Foreign_key'), $row->offsetGet('Foreign_uuid'), $metaUuid);
            $a[$o->foreignUuid] = $o;
        }
        
        /** @var LinkObject $link */
        foreach ($this->links as $link) {
            $a[$link->foreignUuid] = $link;
        }
        
        /** @var LinkObject $link */
        foreach ($this->linksToRemove as $link) {
            unset($a[$link->foreignUuid]);
        }
        
        return new \ArrayIterator($a);
    }
    
    
}