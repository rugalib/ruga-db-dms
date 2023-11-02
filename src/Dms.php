<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms;

use Ruga\Dms\Driver\LibraryDriverInterface;
use Ruga\Dms\Library\Library;
use Ruga\Dms\Library\LibraryInterface;

class Dms
{
    /**
     * @param LibraryDriverInterface $libraryAdapter
     * @param array                  $config
     *
     * @return LibraryInterface
     * @deprecated Use LibraryFactory class
     */
    public static function libraryFactory(LibraryDriverInterface $libraryAdapter, array $config): LibraryInterface
    {
        return new Library($libraryAdapter, $config);
    }
}