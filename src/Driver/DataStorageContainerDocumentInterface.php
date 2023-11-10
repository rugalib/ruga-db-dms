<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

use Ruga\Dms\Document\DocumentInterface;

interface DataStorageContainerDocumentInterface
{
    
    /**
     * Return the content from the data backend.
     *
     * @return string
     */
    public function getContent(): string;
    
    
    
    /**
     * Send content to the data backend of the document.
     * Calls save() on meta and data backend.
     * Returns true if the file content has changed.
     *
     * @param string                  $data
     * @param \DateTimeImmutable|null $lastModified
     *
     * @return bool True if document has changed
     */
    public function setContent(string $data, ?\DateTimeImmutable $lastModified=null): bool;
    
    
    
    /**
     * Returns the size of the content in bytes.
     *
     * @return int
     */
    public function getContentLength(): int;
    
    
}