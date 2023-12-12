<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver;

use Ruga\Db\Row\RowInterface;

/**
 * Interface for a LinkStorageContainer. A LinkStorageContainer stores all the links for a DMS document.
 * Methods in this interface are also present in the document interface.
 */
interface LinkStorageContainerDocumentInterface
{
    
    /**
     * Link document to the entity represented by the given key.
     *
     * @param mixed $key
     */
    public function linkTo($key);
    
    
    
    /**
     * Unlink document from the entity represented by the given key.
     *
     * @param mixed $key
     */
    public function unlinkFrom($key);
    
    
    
    /**
     * Check, if a link has the given foreign key.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function isLinkedTo($key): bool;
    
    
    
    /**
     * Retrieve the links associated with the current object.
     *
     * @return \ArrayIterator The links associated with the current object.
     */
    public function getLinks(): \ArrayIterator;
    
}