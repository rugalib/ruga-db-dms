<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;

use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * DMS library.
 * A library is the root of the document store. It defines where and how to store metadata and content.
 */
class Library extends AbstractLibrary implements LibraryInterface
{
    const CONFIG_LIBRARYSTORAGE = 'library-storage';
    const CONFIG_METASTORAGE = 'meta-storage';
    const CONFIG_DATASTORAGE = 'data-storage';
//    private MetaAdapterInterface $metaAdapter;
//    private DataAdapterInterface $dataAdapter;
    
    
    public function __construct(LibraryDriverInterface $libraryDriver)
    {
        $this->libraryDriver = $libraryDriver;

//        $this->metaAdapter = MetaAdapterFactory::factory($config[self::CONFIG_METASTORAGE] ?? []);
//        $this->dataAdapter = DataAdapterFactory::factory($config[self::CONFIG_DATASTORAGE] ?? []);
    }
    
    
}
