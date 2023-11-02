<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Dms\Dms;
use Ruga\Dms\Library\Library;

class DbDriverFactory implements FactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
//        $config = $container->get('config')[Dms::class][Library::CONFIG_LIBRARYSTORAGE] ?? [];
        $dbadaptername = $options['adapter'] ?? $config['adapter'] ?? AdapterInterface::class;
        $adapter = $container->get($dbadaptername);
        return new DbDriver($adapter);
    }
}