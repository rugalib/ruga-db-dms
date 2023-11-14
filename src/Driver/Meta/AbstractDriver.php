<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Meta;

use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Library\LibraryInterface;

abstract class AbstractDriver implements MetaDriverInterface
{
    private LibraryInterface $library;
    
    
    
    public function __construct(LibraryInterface $library)
    {
        $this->library = $library;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getLibrary(): LibraryInterface
    {
        return $this->library;
    }
    
    
}