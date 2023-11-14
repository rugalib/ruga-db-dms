<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ruga\Dms\Document\DocumentInterface;

interface DataStorageContainerInterface extends DataStorageContainerDocumentInterface
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
     * @return DocumentInterface
     */
    public function getDocument(): DocumentInterface;
    
    
    
    /**
     * Return the data driver.
     *
     * @return DataDriverInterface
     */
    public function getDataDriver(): DataDriverInterface;
    
    
    
    /**
     * Return the unique id of the data record.
     * This Uuid is the same for the entire lifetime of the data record.
     *
     * @return string
     * @deprecated
     */
    public function getUuid(): string;
    
    
    
    /**
     * Presists the document data to the storage backends.
     *
     * @return void
     */
    public function save();
    
    
    
    /**
     * Rename the file on storage, if applicable.
     *
     * @param string $newname
     *
     * @return void
     */
    public function rename(string $newname);
    
    
    
    /**
     * Delete the file.
     *
     * @return void
     */
    public function delete();
}