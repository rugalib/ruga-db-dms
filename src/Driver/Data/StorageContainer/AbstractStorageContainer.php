<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Data\StorageContainer;

use Ramsey\Uuid\Uuid;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Driver\DataStorageContainerInterface;

abstract class AbstractStorageContainer implements DataStorageContainerInterface
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
    public function getUuid(): string
    {
        $hashedUuid = Uuid::uuid5(Uuid::NAMESPACE_OID, hash('sha256', spl_object_hash($this)));
        return $hashedUuid->toString();
    }
    
    
}