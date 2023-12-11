<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Ruga\Dms\Library\LibraryManager;

/**
 * This factory creates a DmsMiddleware. DmsMiddleware is responsible for handling all the requests for
 * a specific document library.
 *
 * @see     DatatablesMiddleware
 */
class DmsMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        $libraryManager = $container->get(LibraryManager::class);
        return new DmsMiddleware($libraryManager);
    }
}
