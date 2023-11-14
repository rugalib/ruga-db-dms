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

interface MetaStorageContainerInterface extends MetaStorageContainerDocumentInterface
{
    /**
     * Set the document object. This function is used by the adapter to deliver the corresponding document object to
     * the storage object.
     *
     * @param DocumentInterface $document
     *
     * @return void
     */
    public function setDocument(DocumentInterface $document);
    
    
    
    /**
     * Return the stored document object.
     *
     * @return null|DocumentInterface
     */
    public function getDocument(): ?DocumentInterface;
    
    
    
    /**
     * Return the meta driver.
     *
     * @return MetaDriverInterface
     */
    public function getMetaDriver(): MetaDriverInterface;
    
    
    
    /**
     * Returns the stored file hash.
     *
     * @return string|null
     */
    public function getHash(): ?string;
    
    
    
    /**
     * Store the file hash.
     *
     * @param string $hash
     *
     * @return void
     */
    public function setHash(string $hash);
    
    
    
    /**
     * Persist the document meta to the storage backends.
     *
     * @return void
     */
    public function save();
    
    
    
    /**
     * Delete the meta data.
     *
     * @return void
     */
    public function delete();
    
    
    
    /**
     * Calculate the hash of the given data.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function calculateHash(string $data);
    
    
    
    /**
     * Store the MIME type.
     * This is only used by the data storage container to set the mime type by the content.
     *
     * @param string $mimetype
     *
     * @return void
     */
    public function setMimetype(string $mimetype);
    
    
    
    /**
     * Get the key to identify the content from data backend. For file based backend this is the physical filename.
     *
     * @return string|null
     */
    public function getDataUniqueKey(): ?string;
    
    
    
    /**
     * Set the key to identify the content from data backend. For file based backend this is the physical filename.
     *
     * @param string $key
     *
     * @return void
     */
    public function setDataUniqueKey(string $key);
    
    
    
    /**
     * Sets the last modified date to the given value.
     *
     * @param \DateTimeImmutable $lastModified
     *
     * @return mixed
     */
    public function setLastModified(\DateTimeImmutable $lastModified);
    
}