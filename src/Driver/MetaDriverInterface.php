<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Library\LibraryInterface;

interface MetaDriverInterface
{
    
    /**
     * Create a new storage object for meta data.
     *
     * @return MetaStorageContainerInterface
     */
    public function createStorage(): MetaStorageContainerInterface;
    
    
    
    /**
     * Find meta storage containers by UUID.
     *
     * @param array|string|UuidInterface $uuid
     *
     * @return \ArrayIterator
     */
    public function findByUuid($uuid): \ArrayIterator;
    
    
    
    /**
     * Dump the current config.
     *
     * @return array
     */
    public function dumpConfig(): array;
    
    
    
    /**
     * Persist the meta to the storage backend.
     *
     * @return mixed
     */
    public function save();
    
    
    
    /**
     * Return the library instance.
     *
     * @return LibraryInterface
     */
    public function getLibrary(): LibraryInterface;
    
}