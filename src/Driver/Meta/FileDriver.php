<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Meta;

use Laminas\Json\Json;
use Ruga\Dms\Driver\Meta\StorageContainer\MemoryStorageContainer;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Library\LibraryInterface;

class FileDriver extends AbstractDriver implements MetaDriverInterface
{
    private string $filepath;
    private \SplObjectStorage $storage;
    
    
    
    public function __construct(LibraryInterface $library, array $options = [])
    {
        parent::__construct($library);
        
        $this->filepath = $options['filepath'] ?? null;
        $this->storage = new \SplObjectStorage();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): MetaStorageContainerInterface
    {
        $container = new MemoryStorageContainer($this);
        $this->storage->attach($container, $container->getUuid());
        return $container;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByUuid($uuid): \ArrayIterator
    {
        $a = [];
        /** @var MetaStorageContainerInterface $item */
        foreach ($this->storage as $item) {
            if ($item->getUuid() === $uuid) {
                $a[] = $item;
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
        $config['driver'] = \Ruga\Dms\Driver\Meta\FileDriver::class;
        $config['filepath'] = $this->filepath;
        return $config;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        $a = [];
        /** @var MetaStorageContainerInterface $item */
        foreach ($this->storage as $item) {
            $a[] = $item->toArray();
        }
        
        file_put_contents($this->filepath, Json::encode($a, true, ['prettyPrint' => true]));
    }
}