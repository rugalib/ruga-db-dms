<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Data;

use Ruga\Dms\Driver\Data\StorageContainer\MemoryStorageContainer;
use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * This data driver stores content in memory.
 */
class MemoryDriver implements DataDriverInterface
{
    private array $storage = [];
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): DataStorageContainerInterface
    {
        $container = new MemoryStorageContainer($this);
        $this->storage[$container->getUuid()] = $container;
        return $container;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByMetaStorage(MetaStorageContainerInterface $metaStorage): DataStorageContainerInterface
    {
        return $metaStorage->getDocument()->getDataStorageContainer();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = \Ruga\Dms\Driver\Data\MemoryDriver::class;
        return $config;
    }
    
    
    
    /**
     * Data is always stored im memory, save() makes no sense.
     *
     * @inheritDoc
     */
    public function save()
    {
    }
    
    
}