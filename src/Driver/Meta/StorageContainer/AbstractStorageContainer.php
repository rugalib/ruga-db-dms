<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Meta\StorageContainer;

use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;

abstract class AbstractStorageContainer implements MetaStorageContainerInterface
{
    private DocumentInterface $document;
    
    
    
    /**
     * @inheritdoc
     */
    public function setDocument(DocumentInterface $document)
    {
        $this->document = $document;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function calculateHash(string $data)
    {
        return hash('sha256', $data);
    }
    
}