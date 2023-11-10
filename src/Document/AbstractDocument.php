<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Document;

use Ruga\Dms\Driver\DataStorageContainerInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Library\AbstractLibrary;
use Ruga\Dms\Library\LibraryInterface;

abstract class AbstractDocument implements DocumentInterface
{
    protected LibraryInterface $library;
    protected MetaStorageContainerInterface $metaStorage;
    protected DataStorageContainerInterface $dataStorage;
    
    
    
    public function __construct(
        AbstractLibrary $library,
        MetaStorageContainerInterface $metaStorage,
        DataStorageContainerInterface $dataStorage
    ) {
        $this->library = $library;
        $this->metaStorage = $metaStorage;
        $this->metaStorage->setDocument($this);
        $this->dataStorage = $dataStorage;
        $this->dataStorage->setDocument($this);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getMetaStorageContainer(): MetaStorageContainerInterface
    {
        return $this->metaStorage;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getDataStorageContainer(): DataStorageContainerInterface
    {
        return $this->dataStorage;
    }
    
    
}