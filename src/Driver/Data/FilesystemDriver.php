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

/**
 * This data driver stores files in the filesystem under a given base path using their real filename. If content is
 * added via \Ruga\Dms\Document\Document::setContentFromFile(), the filename (without path) is automatically used. Use
 * \Ruga\Dms\Document\Document::setFilename() to set a custom filename.
 */
class FilesystemDriver implements DataDriverInterface
{
    private \SplObjectStorage $storage;
    private string $basepath;
    
    
    
    public function __construct(array $options = [])
    {
        $this->storage = new \SplObjectStorage();
        
        $basepath = rtrim($options['basepath'] ?? '', " \t\n\r\0\x0B\\/");
        if (empty($basepath)) {
            throw new \InvalidArgumentException("'basepath' is missing or empty");
        }
        
        $basepath = realpath($basepath);
        if ($basepath === false) {
            throw new \InvalidArgumentException("'basepath' '{$basepath}' is not valid.");
        }
        
        $this->basepath = $basepath;
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
        if ($metaStorage->getDocument()) {
            return $metaStorage->getDocument()->getDataStorageContainer();
        }
        return $this->createStorage();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = \Ruga\Dms\Driver\Data\FilesystemDriver::class;
        $config['basepath'] = $this->basepath;
        return $config;
    }
    
    
    
    /**
     * FilesystemDriver does not need a save() method.
     *
     * @inheritDoc
     *
     */
    public function save()
    {
    }
    
    
    
    /**
     * Return the basepath.
     *
     * @return string
     */
    public function getBasepath(): string
    {
        return $this->basepath;
    }
    
    
    
    /**
     * Return the filename and path for the data file in the filesystem. First tries to read the path from
     * DataUniqueKey, which is actually the primary source for this information. If there is no DataUniqueKey, fall
     * back to the filename. If even a filename is not set, return an empty string.
     *
     * @param DataStorageContainerInterface $dataStorageContainer
     *
     * @return string
     */
    public function getDataFilename(DataStorageContainerInterface $dataStorageContainer): string
    {
        return $dataStorageContainer->getDocument()->getMetaStorageContainer()->getDataUniqueKey()
            ?? $dataStorageContainer->getDocument()->getMetaStorageContainer()->getFilename()
            ?? '';
    }
    
    
    
    /**
     * Set the filename and path for the data file in the filesystem.
     *
     * @param DataStorageContainerInterface $dataStorageContainer
     * @param                               $dataFilename
     *
     * @return void
     */
    public function setDataFilename(DataStorageContainerInterface $dataStorageContainer, $dataFilename)
    {
        $dataStorageContainer->getDocument()->getMetaStorageContainer()->setDataUniqueKey($dataFilename);
    }
    
}