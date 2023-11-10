<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ruga\Db\Row\RowInterface;

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
     * @param array|string $uuid
     *
     * @return \ArrayIterator
     */
    public function findByUuid($uuid): \ArrayIterator;
    
    
    
    /**
     * Find documents by linked entity and by category.
     *
     * @param RowInterface $row
     *
     * @param null         $categories
     *
     * @return \ArrayIterator
     * @deprecated
     */
    public function findByObject(RowInterface $row, $categories = null): \ArrayIterator;
    
}