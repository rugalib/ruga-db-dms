<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Document;

use Laminas\Db\RowGateway\AbstractRowGateway;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Row\RowInterface;
use Ruga\Dms\Driver\DataStorageContainerDocumentInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerDocumentInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Library\AbstractLibrary;
use Ruga\Dms\Library\LibraryInterface;

/**
 * Implementation of a document in the DMS library. This is a common place of access for the developer, using the DMS.
 * It interacts with the concrete adapters to write and read meta and data to and from the backends.
 *
 */
class Document extends AbstractDocument implements DocumentInterface
{
    
    
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->metaStorage->getName();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setName(string $name)
    {
        $this->metaStorage->setName($name);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getFilename(): ?string
    {
        return $this->metaStorage->getFilename();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setFilename(string $name)
    {
        $name = ltrim($name, '/\\');
        $name = str_replace(["/", "\\"], DIRECTORY_SEPARATOR, $name);
        if (!preg_match("/^[^\/:*?\"<>|]+(\.[^\/:*?\"<>|]+)?$/", $name)) {
            throw new \InvalidArgumentException("'{$name}' is not a valid file name");
        }
        
        $oldname = $this->getFilename();
        if ($oldname != $name) {
            $this->dataStorage->rename($name);
            $this->metaStorage->setFilename($name);
        }
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getCategory(): ?string
    {
        return $this->metaStorage->getCategory();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setCategory(string $category)
    {
        $this->metaStorage->setCategory($category);
    }
    
    
    
    /**
     * Get the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product vs. datasheet vs. downloads
     *
     * @return DocumentType
     * @see DocumentType
     */
    public function getDocumentType(): DocumentType
    {
        return $this->metaStorage->getDocumentType();
    }
    
    
    
    /**
     * Set the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product.
     *
     * @return void
     * @see DocumentType
     */
    public function setDocumentType(DocumentType $documentType)
    {
        $this->metaStorage->setDocumentType($documentType);
    }
    
    
    
    /**
     * Gibt die Dateierweiterung anhand des MIME-Types zurück.
     *
     * @see  http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     * @return string
     * @todo Should we place this in a separate library?
     */
    private function getExtensionFromMimetype()
    {
        $mimetype = $this->metaStorage->getMimetype();
        if (empty($mimetype)) {
            return '';
        }
        
        $mimetypes = [
            'text/plain' => 'txt',
            'image/png' => 'png',
            'image/bmp' => 'bmp',
            'image/cgm' => 'cgm',
            'image/g3fax' => 'g3',
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/svg+xml' => 'svg',
            'image/tiff' => 'tif',
            'image/vnd.adobe.photoshop' => 'psd',
            'image/vnd.dxf' => 'dxf',
            'image/x-icon' => 'ico',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
        ];
        
        [$mimetype, $dummy] = explode(';', $mimetype, 2);
        return $mimetypes[$mimetype] ?? 'bin';
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getUuid(): UuidInterface
    {
        return $this->metaStorage->getUuid();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->dataStorage->save();
        $this->metaStorage->save();
        $this->linkStorage->save();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return $this->dataStorage->getContent();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setContent(string $data, ?\DateTimeImmutable $lastModified = null): bool
    {
        $lastModified = $lastModified ?? (new \DateTimeImmutable());
        try {
            // Determine mime type and store in meta backend
            $finfo = new \finfo(FILEINFO_MIME);
            $this->metaStorage->setMimetype($finfo->buffer($data));
            
            // Use the name (self::getName()) as filename if no filename is set
            if (!$this->getFilename()) {
                $path_parts = pathinfo($this->getName());
                
                $path_parts['extension'] = $path_parts['extension'] ?? $this->getExtensionFromMimetype();
                $name = implode('.', array_filter([$path_parts['filename'], $path_parts['extension']]));
                
                $this->setFilename($name);
            }
            return $this->dataStorage->setContent($data, $lastModified);
        } finally {
            $this->save();
        }
    }
    
    
    
    /**
     * Saves the content to the given filename.
     *
     * @param string $file
     *
     * @return bool
     */
    public function getContentToFile(string $file): bool
    {
        if (file_exists($file)) {
            throw new FileAlreadyExistsException("The file '{$file}' already exists");
        }
        return file_put_contents($file, $this->getContent()) !== false;
    }
    
    
    
    /**
     * Saves the content to the given directory using the filename from meta backend.
     *
     * @param string $path
     *
     * @return string
     */
    public function getContentToDirectory(string $path): string
    {
        $file = trim($path, "\\/") . "/" . $this->getFilename();
        return $this->getContentToFile($file) ? $file : '';
    }
    
    
    
    /**
     * Read content from a file and send it to the data backend.
     * Calls save() on meta and data backend.
     * Returns true if the file content has changed.
     *
     * @param string $file
     * @param bool   $deleteFileAfterImport
     *
     * @return bool
     * @throws \Exception
     */
    public function setContentFromFile(string $file, bool $deleteFileAfterImport = false): bool
    {
        if (!($fn = realpath($file))) {
            throw new \InvalidArgumentException("File '{$file}' not found.");
        }
        
        // Use the filename of the input file, if no filename is set yet.
        if (!$this->getFilename()) {
            $this->setFilename(basename($fn));
        }
        
        try {
            $lastModified = \DateTimeImmutable::createFromFormat('U', strval(filemtime($fn)));
            return $this->setContent(file_get_contents($fn), $lastModified);
        } finally {
            if ($deleteFileAfterImport) {
                unlink($fn);
            }
        }
    }
    
    
    
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
    public function setContentFromTemplate(string $templatefile, array $data = []): bool
    {
        \Ruga\Log::functionHead();
        
        if (!($fn = realpath($templatefile))) {
            throw new \InvalidArgumentException("Template file '{$templatefile}' not found.");
        }
        
        $content = (function ($templatefile, $data) {
            extract($data, EXTR_SKIP);
            unset($data);
            try {
                ob_start();
                include $templatefile;
                $str = ob_get_contents();
                return $str;
            } catch (\Throwable $e) {
                \Ruga\Log::addLog($e);
                throw $e;
            } finally {
                ob_end_clean();
            }
        })(
            $fn,
            $data
        );
        
        // Use the filename of the template file, if no filename is set yet.
        if (!$this->getFilename()) {
            $this->setFilename(basename($fn));
        }
        
        return $this->setContent($content);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->linkStorage->delete();
        $this->dataStorage->delete();
        $this->metaStorage->delete();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function linkTo($key)
    {
        return $this->linkStorage->linkTo($key);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function unlinkFrom($key)
    {
        return $this->linkStorage->unlinkFrom($key);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function isLinkedTo($key): bool
    {
        return $this->linkStorage->isLinkedTo($key);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getDownloadUri(string $basePath = ''): \Psr\Http\Message\UriInterface
    {
        $basePath = trim($basePath, " /\\\t\n\r\0\x0B");
        if (empty($basePath)) {
            $basePath = null;
        }
        
        return new Uri(
            implode(
                '/',
                array_filter(
                    [
                        $basePath,
                        'ruga-db-dms',
                        'download',
                        "{$this->getUuid()}",
                        "{$this->getFilename()}",
                    ],
                    function ($value) {
                        return ($value !== null);
                    }
                )
            )
        );
    }
    
    
    
    /**
     * Return the content as stream.
     *
     * @return StreamInterface
     * @todo Request stream from data backend
     */
    public function getContentStream(): StreamInterface
    {
        return (new StreamFactory())->createStream($this->dataStorage->getContent());
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getContentLength(): int
    {
        return $this->dataStorage->getContentLength();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getLastModified(): \DateTimeImmutable
    {
        return $this->metaStorage->getLastModified();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getMimetype(): string
    {
        return $this->metaStorage->getMimetype();
    }
    
    
}