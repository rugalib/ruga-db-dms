<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Meta;

use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Document\Document;
use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\Meta\StorageContainer\MemoryStorageContainer;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

class MemoryDriver extends AbstractDriver implements MetaDriverInterface
{
    private array $storage = [];
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): MetaStorageContainerInterface
    {
        $container = new MemoryStorageContainer($this);
        $this->storage[$container->getUuid()] = $container;
        return $container;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByUuid($uuid): \ArrayIterator
    {
        $a = [];
        $uuids = is_array($uuid) ? $uuid : [$uuid];
        foreach ($uuids as $uuid) {
            if (array_key_exists($uuid, $this->storage)) {
                $a[] = $this->storage[$uuid];
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
        $config['driver'] = \Ruga\Dms\Driver\Meta\MemoryDriver::class;
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