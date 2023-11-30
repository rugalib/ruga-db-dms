<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver;


/**
 * Interface for a library driver. Defines all necessary functions a library storage driver has to provide.
 */
interface LibraryDriverInterface
{
    const ATTR_NAME = 'name';
    
    
    
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
    
    
    
    /**
     * Dump the current config.
     *
     * @return array
     */
    public function dumpConfig(): array;
    
    
    
    /**
     * Set the config.
     *
     * @param array $config
     *
     * @return mixed
     */
    public function setConfig(array $config);
    
    
    
    /**
     * Persist the library to the storage backend.
     *
     * @return mixed
     */
    public function save();
    
}