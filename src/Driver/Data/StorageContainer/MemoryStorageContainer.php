<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ruga\Dms\Driver\DataStorageContainerInterface;

class MemoryStorageContainer extends AbstractStorageContainer implements DataStorageContainerInterface
{
    private string $content;
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // MemoryStorageContainer has no save function
        // calling parent driver's save()
        $this->getDataDriver()->save();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setContent(string $data, ?\DateTimeImmutable $lastModified = null): bool
    {
        $metaStorage = $this->getDocument()->getMetaStorageContainer();
        $newhash = $metaStorage->calculateHash($data);
        // Hash changed => persist new content to the data backend
        if ($newhash != $metaStorage->getHash()) {
            $this->content = $data;
            $metaStorage->setHash($newhash);
            $lastModified = $lastModified ?? (new \DateTimeImmutable());
            $metaStorage->setLastModified($lastModified);
            return true;
        }
        return false;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
        // Not applicable
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        unset($this->content);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getContentLength(): int
    {
        return strlen($this->content);
    }
}