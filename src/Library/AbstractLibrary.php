<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;


use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * Abstract template.
 */
abstract class AbstractLibrary implements LibraryInterface
{
    protected LibraryDriverInterface $libraryDriver;
    
    
    
    public function getName(): string
    {
        return $this->libraryDriver->getName();
    }
    
    
    
    public function setName(string $name)
    {
        $this->libraryDriver->setName($name);
    }
}
