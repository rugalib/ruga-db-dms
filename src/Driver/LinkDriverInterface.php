<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ramsey\Uuid\UuidInterface;

/**
 * Interface for the link storage driver. The link storage driver manages containers that contain links between one
 * DMS meta object and one or more external objects.
 */
interface LinkDriverInterface
{
    /**
     * Create a new storage object for link data.
     *
     * @param UuidInterface $metaUuid
     *
     * @return LinkStorageContainerInterface
     */
    public function createStorage(UuidInterface $metaUuid): LinkStorageContainerInterface;
    
    
    
    /**
     * Returns the link storage container that belongs to the given meta storage.
     *
     * @param MetaStorageContainerInterface $metaStorage
     *
     * @return LinkStorageContainerInterface
     */
    public function findByMetaStorage(MetaStorageContainerInterface $metaStorage): LinkStorageContainerInterface;
    
    
    
    /**
     * Find all the link storage containers linked to a given foreign key.
     *
     * @param mixed $key
     *
     * @return \ArrayIterator
     */
    public function findByForeignKey($key): \ArrayIterator;
    
    
    
    /**
     * Dump the current config.
     *
     * @return array
     */
    public function dumpConfig(): array;
    
    
    
    /**
     * Persist the links to the storage backend.
     *
     * @return mixed
     */
    public function save();
    
}