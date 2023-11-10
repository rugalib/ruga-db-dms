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

class MemoryDriver implements DataDriverInterface
{
    private array $storage = [];
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): DataStorageContainerInterface
    {
        $container = new MemoryStorageContainer();
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
}