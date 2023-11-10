<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Document;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Ruga\Dms\Driver\DataStorageContainerDocumentInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\LinkStorageContainerDocumentInterface;
use Ruga\Dms\Driver\LinkStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerDocumentInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * Interface to a general document in the DMS.
 */
interface DocumentInterface extends MetaStorageContainerDocumentInterface,
                                    DataStorageContainerDocumentInterface,
                                    LinkStorageContainerDocumentInterface
{
    
    
    /**
     * Persist the document meta and data to the storage backends.
     *
     * @return void
     */
    public function save();
    
    
    
    /**
     * Delete link, data and meta record.
     *
     * @return void
     */
    public function delete();
    
    
    
    /**
     * Saves the content to the given filename.
     *
     * @param string $file
     *
     * @return bool
     */
    public function getContentToFile(string $file): bool;
    
    
    
    /**
     * Saves the content to the given directory using the filename from meta backend.
     *
     * @param string $path
     *
     * @return string
     */
    public function getContentToDirectory(string $path): string;
    
    
    
    /**
     * Read content from a file and send it to the data backend.
     * Calls save() on meta and data backend.
     * Returns true if the file content has changed.
     *
     * @param string $file
     * @param bool   $deleteFileAfterImport
     *
     * @return bool True if document has changed
     */
    public function setContentFromFile(string $file, bool $deleteFileAfterImport = false): bool;
    
    
    
    /**
     * Executes the given $templatefile with an include and saves the resulting content to the document.
     * Calls save() on meta nd data backend.
     * Returns true if the file content has changed.
     *
     * @param string $templatefile
     * @param array  $data
     *
     * @return bool True if document has changed
     * @throws \Exception
     */
    public function setContentFromTemplate(string $templatefile, array $data = []): bool;
    
    
    
    /**
     * Return the content as stream.
     *
     * @return StreamInterface
     */
    public function getContentStream(): StreamInterface;
    
    
    
    /**
     * Returns the uri where the document can be downloaded.
     *
     * @param string $basePath
     *
     * @return UriInterface
     */
    public function getDownloadUri(string $basePath = ''): \Psr\Http\Message\UriInterface;
    
    
    
    /**
     * Return the meta storage container associated to this document.
     *
     * @return MetaStorageContainerInterface
     */
    public function getMetaStorageContainer(): MetaStorageContainerInterface;
    
    
    
    /**
     * Return the data storage container associated to this document.
     *
     * @return DataStorageContainerInterface
     */
    public function getDataStorageContainer(): DataStorageContainerInterface;
    
    
    
    /**
     * Return the link storage container associated to this document.
     *
     * @return LinkStorageContainerInterface
     */
    public function getLinkStorageContainer(): LinkStorageContainerInterface;
    
}