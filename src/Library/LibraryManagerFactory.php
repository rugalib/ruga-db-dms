<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;

use Psr\Container\ContainerInterface;
use Ruga\Dms\Dms;

/**
 * @see LibraryManager
 */
class LibraryManagerFactory
{
    public function __invoke(ContainerInterface $container): LibraryManager
    {
        $config = ($container->get('config') ?? [])[Dms::class][Dms::CONF_LIBRARY_MANAGER] ?? [];
        return new LibraryManager($container, $config);
    }
    
}