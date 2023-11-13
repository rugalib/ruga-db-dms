<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Document\DocumentType;

interface MetaStorageContainerDocumentInterface
{
    
    
    /**
     * Get the name of the document.
     *
     * @return string
     * @see self::setName()
     *
     */
    public function getName(): string;
    
    
    
    /**
     * Set the name of the document. This is the identifier the document is referenced by in the application.
     * It does not have to be the same as the file name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name);
    
    
    
    /**
     * Returns the filename or null if it is not set. This is not necessarily the file name, where the content is saved.
     * This file name is meant for presentation to the user (ex. downloads, lists, ...)
     *
     *
     * @return string|null
     * @see self::setFilename()
     */
    public function getFilename(): ?string;
    
    
    
    /**
     * Set the name of the file for downloading. Depending on the data storage backend, this name is also used for
     * content storage.
     * If this function is called again with a different name, the file is renamed in the backend (if applicable).
     * This function saves the data and meta backend if the $name changes.
     *
     * @param string $name
     *
     * @return void
     * @see self::getFilename()
     */
    public function setFilename(string $name);
    
    
    
    /**
     * Get the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product vs. datasheet vs. downloads
     *
     * @return DocumentType
     * @see DocumentType
     */
    public function getDocumentType(): DocumentType;
    
    
    
    /**
     * Set the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product vs. datasheet vs. downloads
     *
     * @param DocumentType $documentType
     *
     * @return void
     * @see DocumentType
     */
    public function setDocumentType(DocumentType $documentType);
    
    
    
    /**
     * Returns the stored MIME type.
     *
     * @return string
     */
    public function getMimetype(): string;
    
    
    
    /**
     * Returns the category of the document.
     *
     * @return string|null
     */
    public function getCategory(): ?string;
    
    
    
    /**
     * Set the category of the document.
     *
     * @param string $category
     *
     * @return mixed
     */
    public function setCategory(string $category);
    
    
    
    /**
     * Return the unique id of the meta record.
     * This Uuid is the same for the entire lifetime of the meta record.
     *
     * @return string
     */
    public function getUuid(): string;
    
    
    
    /**
     * Return the date and time when the document was last modified.
     *
     * @return \DateTimeImmutable
     */
    public function getLastModified(): \DateTimeImmutable;
    
    
}