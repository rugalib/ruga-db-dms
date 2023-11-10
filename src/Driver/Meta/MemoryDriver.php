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

class MemoryDriver implements MetaDriverInterface
{
    private array $storage = [];
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): MetaStorageContainerInterface
    {
        $container = new MemoryStorageContainer();
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
        foreach($uuids as $uuid) {
            if (array_key_exists($uuid, $this->storage)) {
                $a[] = $this->storage[$uuid];
            }
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByObject(RowInterface $row, $categories = null): \ArrayIterator
    {
        // TODO: Implement findByObject() method.
        $a = [];
        /** @var MetaStorageContainerInterface $metaStorage */
        foreach ($this->metaAdapter->findByObject($row, $categories) as $metaStorage) {
            if ($metaStorage->getLibrary() != $this->getName()) {
                continue;
            }
            /** @var DataStorageContainerInterface $dataStorage */
            $dataStorage = $this->dataAdapter->findByMetaStorage($metaStorage);
            $a[] = new Document($this, $metaStorage, $dataStorage);
        }
        return new \ArrayIterator($a);
        
    }
}