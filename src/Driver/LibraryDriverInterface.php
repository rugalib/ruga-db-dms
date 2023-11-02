<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver;


interface LibraryDriverInterface
{
    CONST ATTR_NAME = 'name';
    
    /**
     * Return the name of the library.
     *
     * @return string
     */
    public function getName(): string;
    
    
    
    /**
     * Set the name of the library.
     *
     * @return string
     */
    public function setName(string $name);
    
}