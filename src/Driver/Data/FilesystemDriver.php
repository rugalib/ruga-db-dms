<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data;

use Ruga\Dms\Driver\Data\StorageContainer\FileStorageContainer;
use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

class FilesystemDriver implements DataDriverInterface
{
    private \SplObjectStorage $storage;
    private string $basepath;
    
    
    
    public function __construct(array $options = [])
    {
        $this->storage = new \SplObjectStorage();
        $basepath = rtrim($options['basepath'] ?? '', " \t\n\r\0\x0B\\/");
        $basepath = realpath($basepath);
        if ($basepath !== false) {
            $this->basepath = $basepath;
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): DataStorageContainerInterface
    {
        $container = new FileStorageContainer($this);
        $this->storage->attach($container, $container->getUuid());
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
        return $config;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // TODO: Implement save() method.
    }
    
    
    
    public function getBasepath(): string
    {
        return $this->basepath;
    }
    
    
    
    public function getDataFilename(DataStorageContainerInterface $dataStorageContainer): string
    {
        return $dataStorageContainer->getDocument()->getMetaStorageContainer()->getFilename() ?? '';
    }
    
    
    
    public function setDataFilename(DataStorageContainerInterface $dataStorageContainer, $dataFilename)
    {
        $dataStorageContainer->getDocument()->getMetaStorageContainer()->setDataUniqueKey($dataFilename);
    }
    
}