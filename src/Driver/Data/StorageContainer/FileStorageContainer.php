<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data\StorageContainer;

use Laminas\Diactoros\Stream;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;

/**
 * Store content data in a file on a filesystem.
 *
 * @see \Ruga\Dms\Driver\Data\FilesystemDriver
 * @see \Ruga\Dms\Driver\Data\ObjectstorageDriver
 */
class FileStorageContainer extends AbstractStorageContainer implements DataStorageContainerInterface
{
    
    /**
     * Get content file name from the driver. Depending on the driver, this can be a "real" (meaningful for
     * humans and other systems) filename or an encoded path for object storage.
     *
     * @return string
     */
    private function getDataFilename(): string
    {
        return $this->getDataDriver()->getDataFilename($this);
    }
    
    
    
    /**
     * Set the content file name. Is called by self::setContent() after content has been written to the filesystem.
     *
     * @param string $filename
     *
     * @return void
     */
    private function setDataFilename(string $filename)
    {
        $this->getDataDriver()->setDataFilename($this, $filename);
    }
    
    
    
    /**
     * Build an absolute path by using the basepath from driver and the given filename.
     *
     * @param string $filename
     *
     * @return string
     */
    private function buildFilepath(string $filename): string
    {
        $basepath = $this->getDataDriver()->getBasepath();
        return $basepath . DIRECTORY_SEPARATOR . $filename;
    }
    
    
    
    /**
     * Prepare the content file for storage.
     * Creates the directory structure and "touches" the content file.
     *
     * @param string $filepath
     *
     * @return string
     */
    private function prepareFilepath(string $filepath): string
    {
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }
        touch($filepath);
        return $filepath;
    }
    
    
    
    /**
     * Return true, if the content file exists.
     *
     * @param string|null $filename
     *
     * @return bool
     */
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
    public function setStreamContent(Stream $dataStream, ?\DateTimeImmutable $lastModified = null): bool
    {
        $metaStorage = $this->getDocument()->getMetaStorageContainer();
        $filename = $this->getDataFilename();
        
        // If stream is not a file, store contents to a temp file
        if ($dataStream->getMetadata()['wrapper_type'] === 'file://') {
            $tmpfilename = $dataStream->getMetadata()['uri'];
        } else {
            $tmpfile = tmpfile();
            stream_copy_to_stream($dataStream->detach(), $tmpfile);
            $tmpfilename = stream_get_meta_data($tmpfile)['uri'];
        }
        
        $newhash = $metaStorage->calcualteFileHash($tmpfilename);
        // Hash changed => persist new content to the data backend
        if ($newhash != $metaStorage->getHash()) {
//            file_put_contents($this->prepareFilepath($this->buildFilepath($filename)), $data);
            copy($tmpfilename, $this->prepareFilepath($this->buildFilepath($filename)));
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
        if (!$this->fileExists()) {
            return 0;
        }
        $filename = $this->getDataFilename();
        return filesize($this->buildFilepath($filename));
    }
    
    
    
    /**
     * FileStorageContainer does not need a save() method, because content is immediately saved to the file.
     *
     * @inheritDoc
     */
    public function save()
    {
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function rename(string $newname)
    {
        $oldFilename = $this->getDataFilename();
        $this->setDataFilename($newname);
        $newFilename = $this->getDataFilename();
        
        if ($this->fileExists($newname)) {
            throw new \InvalidArgumentException("File '{$newname}' already exists");
        }
        
        if ($this->fileExists($oldFilename) && ($oldFilename != $newFilename)) {
            if (rename(
                $this->buildFilepath($oldFilename),
                $this->prepareFilepath($this->buildFilepath($newFilename))
            )) {
                $this->setDataFilename($newFilename);
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