<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;


use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Document\DocumentType;

/**
 * Interface to a template.
 */
interface LibraryInterface
{
    /**
     * Return the name of the library.
     *
     * @return string
     */
    public function getName(): string;
    
    
    
    /**
     * Set the name of the library.
     *
     * @param string $name
     */
    public function setName(string $name);
    
    
    
    /**
     * Create a new document in the library.
     * The Document is NOT automatically saved after creation.
     *
     * @param string       $name
     * @param DocumentType $documentType
     *
     * @return DocumentInterface
     */
    public function createDocument(string $name, DocumentType $documentType): DocumentInterface;
    
    
    
    /**
     * Find documents by UUID.
     *
     * @param array|string $uuid
     *
     * @return \ArrayIterator
     */
    public function findDocumentsByUuid($uuid): \ArrayIterator;
    
    
    
    /**
     * Find documents by foreign keys. This can be actually anything (ex. uniqueid, object hash, id, ...)
     *
     * @param mixed             $key
     * @param null|array|string $categories
     *
     * @return \ArrayIterator
     */
    public function findDocumentsByForeignKey($key, $categories = null): \ArrayIterator;
    
}
