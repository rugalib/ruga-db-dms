<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver;

class LibraryDriverFactory
{
    
    public function __invoke(array $config): LibraryDriverInterface
    {
        $driver = $config['driver'] ?? null;
        switch ($driver) {
            case 'memory':
                return new Library\MemoryDriver($config);
            
            case 'db':
        }
        throw new \InvalidArgumentException("Driver '{$driver}' is unknown to '" . self::class . "'");
    }
    
}