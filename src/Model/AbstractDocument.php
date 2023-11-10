<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Model;

use Laminas\Db\RowGateway\RowGateway;
use Ruga\Db\Row\AbstractRow;
use Ruga\Db\Row\AbstractRugaRow;
use Ruga\Db\Row\Feature\FullnameFeatureRowInterface;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Document\DocumentType;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

/**
 * Abstract document.
 *
 * @see      Document
 * @see      DocumentAttributesInterface
 */
abstract class AbstractDocument extends AbstractRugaRow implements DocumentAttributesInterface,
                                                                   FullnameFeatureRowInterface
{
    private DocumentInterface $document;
    /** @var RowGateway[] */
    private $linkRows = [];
    
    
    
    /**
     * Constructs a display name from the given fields.
     * Fullname is saved in the row to speed up queries.
     *
     * @return string
     */
//    public function getFullname(): string
//    {
//        return $this->document->getFilename() ?? $this->document->getName();
//    }
    
    
    
    /**
     * Returns the file name used for downloads
     *
     * @return string
     * @throws \Exception
     * @deprecated Do not use this function
     */
    public function getDownloadFilename(): string
    {
        throw new \Exception("deprecated");
    }
    
    
    
    public function toArray(): array
    {
        $aParty = parent::toArray();
        $aParty['html_link'] = "<a href=\"dms/{$this->PK}/edit\">" . $this->fullname . '</a>';
        $aParty['isDisabled'] = $this->isDisabled();
        
        $aParty['isDisabled'] = false;
        $aParty['isDeleted'] = false;
        $aParty['canBeChangedBy'] = true;
        
        
        return $aParty;
    }
    
    
    
    /**
     * Set the document object.
     *
     * @param DocumentInterface $document
     *
     * @return void
     */
    public function setDocument(DocumentInterface $document)
    {
        $this->document = $document;
    }
    
    
    
    /**
     * Get the name of the document.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    
    
    /**
     * Set the name of the document. This is the identifier the document is referenced by in the application.
     * It does not have to be the same as the file name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    
    
    /**
     * Returns the filename or null if it is not set.
     *
     * @return string|null
     * @see MetaStorageInterface::setFilename()
     */
    public function getFilename(): ?string
    {
        return $this->filepath;
    }
    
    
    
    /**
     * Set the name of the file for downloading. Depending on the data storage backend, this name is also used.
     *
     * @param string $name
     *
     * @return void
     */
    public function setFilename(string $name)
    {
        $this->filepath = $name;
    }
    
    
    
    /**
     * Get the key to identify the content from data backend. For file based backend this is the physical filename.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getDataUniqueKey(): ?string
    {
        return $this->data_unique_key;
    }
    
    
    
    /**
     * Set the key to identify the content from data backend. For file based backend this is the physical filename.
     *
     * @param string $key
     *
     * @return void
     */
    public function setDataUniqueKey(string $key)
    {
        $this->data_unique_key = empty($key) ? null : $key;
    }
    
    
    
    /**
     * Get the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product vs. datasheet vs. downloads.
     *
     * @return DocumentType
     * @see DocumentType
     */
    public function getDocumentType(): DocumentType
    {
        return $this->document_type;
    }
    
    
    
    /**
     * Set the type of the document. Primarily used to identify documents of the same kind assigned to an entity.
     * ex. Multiple images to a product vs. datasheet vs. downloads.
     *
     * @return void
     * @see DocumentType
     */
    public function setDocumentType(DocumentType $documentType)
    {
        $this->document_type = $documentType;
    }
    
    
    
    /**
     * Returns the stored hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
    
    
    
    /**
     * Store the hash.
     *
     * @param string $hash
     *
     * @return void
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }
    
    
    
    /**
     * Returns the stored MIME type.
     *
     * @return string|null
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }
    
    
    
    /**
     * Store the MIME type.
     *
     * @param string $mimetype
     *
     * @return void
     */
    public function setMimetype(string $mimetype)
    {
        $this->mimetype = $mimetype;
    }
    
    
    
    /**
     * Returns the name of the library.
     *
     * @return string
     */
    public function getLibrary(): string
    {
        return $this->library;
    }
    
    
    
    /**
     * Set the name of the library.
     *
     * @param string $library
     */
    public function setLibrary(string $library)
    {
        $this->library = $library;
    }
    
    
    
    /**
     * Returns the category of the document.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }
    
    
    
    /**
     * Set the category of the document.
     *
     * @param string $category
     *
     * @return mixed
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
    }
    
    
    
    /**
     * Get the unique id of the meta record.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    
    
    /**
     * Delete
     *
     * @return int
     */
    public function delete()
    {
        parent::delete();
    }
    
    
    
    /**
     * Link document to the given entity.
     * The link is stored in the instance and is persisted together with the document.
     *
     * @param RowInterface $row
     *
     * @return AbstractRowGateway The row of the link table
     * @throws \ReflectionException
     */
    public function linkTo(RowInterface $row): AbstractRowGateway
    {
        // Is there a link table?
        $leftTableName = $row->getTableGateway()->getTable();
        $rightTableName = $this->getTableGateway()->getTable();
        $linkTableDbName = "{$leftTableName}_has_{$rightTableName}";
        $linkTableClassName = (new \ReflectionClass($row))->getShortName() . "Has" . (new \ReflectionClass(
                $this
            ))->getShortName();
//        \Ruga\Log::log_msg("Link table db name: {$linkTableDbName}");
//        \Ruga\Log::log_msg("Link table class name: {$linkTableClassName}");

//        $linkTable=$this->getTableGateway()->getAdapter()->tableFactory($linkTableClassName);
        
        $primaryKey = ["{$leftTableName}_id", "{$rightTableName}_id"];
        
        $linkTable = new GenericLinkTable($linkTableDbName, $this->getTableGateway()->getAdapter(), $primaryKey);
//        \Ruga\Log::log_msg("Link table class name: " . get_class($linkTable));
        
        
        if (!$link = $linkTable->select(
            ["{$leftTableName}_id" => $row->PK, "{$rightTableName}_id" => $this->PK]
        )->current()) {
//            $link=$linkTable->createRow(["{$leftTableName}_id" => $row->PK, "{$rightTableName}_id" => $this->PK]);
            
            $link = new GenericLink($primaryKey, $linkTableDbName, $linkTable->getAdapter());
            $link->{$primaryKey[0]} = $row->PK;
            $link->{$primaryKey[1]} = $this->PK;
        }
        
        
        $this->linkRows[] = $link;
//        \Ruga\Log::log_msg("Link class name: " . get_class($link));
        
        return $link;
    }
    
    
    
    /**
     * Unlink document from the given entity.
     *
     * @return mixed
     * @throws \Exception
     */
    public function unlinkFrom(RowInterface $row)
    {
        // Is there a link table?
        $leftTableName = $row->getTableGateway()->getTable();
        $rightTableName = $this->getTableGateway()->getTable();
        $linkTableDbName = "{$leftTableName}_has_{$rightTableName}";
        $linkTableClassName = (new \ReflectionClass($row))->getShortName() . "Has" . (new \ReflectionClass(
                $this
            ))->getShortName();
        $primaryKey = ["{$leftTableName}_id", "{$rightTableName}_id"];
        $linkTable = new GenericLinkTable($linkTableDbName, $this->getTableGateway()->getAdapter(), $primaryKey);
        
        /** @var GenericLink $link */
        if (!$link = $linkTable->select(
            ["{$leftTableName}_id" => $row->PK, "{$rightTableName}_id" => $this->PK]
        )->current()) {
            throw new DocumentNotLinkedToEntityException(
                "Document '{$this->PK}' is not linked to entity '{$row->PK}' ({$linkTableDbName})"
            );
        }
        
        $link->delete();
    }
    
    
}
