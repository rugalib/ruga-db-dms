<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;


interface DataDriverInterface
{
    /**
     * Create a new storage object for content data.
     *
     * @return DataStorageContainerInterface
     */
    public function createStorage(): DataStorageContainerInterface;
    
    
    
    /**
     * Returns the data storage container that belongs to the given meta storage.
     *
     * @param MetaStorageContainerInterface $metaStorage
     *
     * @return DataStorageContainerInterface
     */
    public function findByMetaStorage(MetaStorageContainerInterface $metaStorage): DataStorageContainerInterface;
    
    
    
    /**
     * Dump the current config.
     *
     * @return array
     */
    public function dumpConfig(): array;
    
    
    
    /**
     * Persist the data to the storage backend.
     *
     * @return mixed
     */
    public function save();
    
}