<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Link;

use Ramsey\Uuid\UuidInterface;
use Ruga\Dms\Driver\Link\StorageContainer\MemoryStorageContainer;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * Stores links to other entities/object in memory.
 */
class MemoryDriver implements LinkDriverInterface
{
    private \SplObjectStorage $storage;
    
    
    
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(UuidInterface $metaUuid): LinkStorageContainerInterface
    {
        $container = new MemoryStorageContainer($this, $metaUuid);
        $this->storage->attach($container, $container);
        return $container;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function findByMetaStorage(MetaStorageContainerInterface $metaStorage): LinkStorageContainerInterface
    {
        return $metaStorage->getDocument()->getLinkStorageContainer();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function findByForeignKey($key): \ArrayIterator
    {
        $a = [];
        /** @var LinkStorageContainerInterface $linkStorageContainer */
        foreach ($this->storage as $linkStorageContainer) {
            if ($linkStorageContainer->isLinkedTo($key)) {
                $a[] = $linkStorageContainer;
            }
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = \Ruga\Dms\Driver\Link\MemoryDriver::class;
        return $config;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // Not applicable
    }
    
    
}