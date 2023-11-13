<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data\StorageContainer;

use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;

class FileStorageContainer extends AbstractStorageContainer implements DataStorageContainerInterface
{
    
    
    private function getDataFilename(): string
    {
        return $this->getDataDriver()->getDataFilename($this);
    }
    
    
    
    private function setDataFilename(string $filename)
    {
        $this->getDataDriver()->setDataFilename($this, $filename);
    }
    
    
    
    private function buildFilepath(string $filename): string
    {
        $basepath = $this->getDataDriver()->getBasepath();
        return $basepath . DIRECTORY_SEPARATOR . $filename;
    }
    
    
    
    private function prepareFilepath(string $filepath): string
    {
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }
        touch($filepath);
        return $filepath;
    }
    
    
    
    private function fileExists(?string $filename = null): bool
    {
        if ($filename === null) {
            $filename = $this->getDataFilename();
        }
        return !empty($filename) && is_file($this->buildFilepath($filename));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $filename = $this->getDataFilename();
        return file_get_contents($this->buildFilepath($filename));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setContent(string $data, ?\DateTimeImmutable $lastModified = null): bool
    {
        $metaStorage = $this->getDocument()->getMetaStorageContainer();
        $filename = $this->getDataFilename();
        $newhash = $metaStorage->calculateHash($data);
        // Hash changed => persist new content to the data backend
        if ($newhash != $metaStorage->getHash()) {
            file_put_contents($this->prepareFilepath($this->buildFilepath($filename)), $data);
            $this->setDataFilename($filename);
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
    public function getContentLength(): int
    {
        $filename = $this->getDataFilename();
        return filesize($this->buildFilepath($filename));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function save()
    {
        // TODO: Implement save() method.
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
        if ($this->fileExists($newname)) {
            throw new \InvalidArgumentException("File '{$newname}' already exists");
        }
        
        if ($this->fileExists()) {
            $filename = $this->getDataFilename();
            if (rename($this->buildFilepath($filename), $this->prepareFilepath($this->buildFilepath($newname)))) {
                $this->getDocument()->getMetaStorageContainer()->setDataUniqueKey($newname);
            }
        }
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function delete()
    {
        $filename = $this->getDataFilename();
        unlink($this->buildFilepath($filename));
    }
}