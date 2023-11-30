<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Link;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Db\Table\AbstractTable;
use Ruga\Dms\Driver\Link\StorageContainer\DbStorageContainer;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Model\Link;
use Ruga\Dms\Model\LinkTable;

/**
 * Stores links to other entities/object in a database.
 */
class DbDriver implements LinkDriverInterface
{
    private \SplObjectStorage $storage;
    private AbstractTable $table;
    
    
    
    public function __construct(array $config)
    {
        $this->storage = new \SplObjectStorage();
        
        if (($config['table'] ?? '') instanceof AbstractTable) {
            $this->table = $config['table'];
            return;
        }
        if ($config['adapter'] instanceof AdapterInterface) {
            $tableclassname = $config['table'] ?? LinkTable::class;
            $this->table = new $tableclassname($config['adapter']);
            return;
        }
        throw new \InvalidArgumentException(
            "'table' must be a \Ruga\Db\Table\AbstractTable instance or 'adapter' must be a \Ruga\Db\Adapter\AdapterInterface instance"
        );
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(UuidInterface $metaUuid): LinkStorageContainerInterface
    {
        $container = new DbStorageContainer($this, $metaUuid, $this->table);
        $this->storage->attach($container);
        return $container;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByMetaStorage(MetaStorageContainerInterface $metaStorage): LinkStorageContainerInterface
    {
        return $metaStorage->getDocument()->getLinkStorageContainer();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByForeignKey($key): \ArrayIterator
    {
        $key = DbStorageContainer::keyFromMixed($key);
        $keyUuid = (Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', $key)))->toString();
        $a = [];
        /** @var Link $row */
        foreach ($this->table->select(['Foreign_uuid' => $keyUuid]) as $row) {
            $uuid = Uuid::fromString($row->offsetGet('Meta_uuid'));
            $a[] = $this->createStorage($uuid);
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = DbDriver::class;
        $config['table'] = $this->table->getTable();
        $config['adapter'] = get_class($this->table->getAdapter());
        return $config;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
    }
}