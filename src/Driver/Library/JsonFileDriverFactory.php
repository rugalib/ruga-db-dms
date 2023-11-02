<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Library;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Ruga\Dms\Dms;
use Ruga\Dms\Library\Library;

class JsonFileDriverFactory implements FactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config')[Dms::class][Library::CONFIG_LIBRARYSTORAGE] ?? [];
        $filepath = $options['filepath'] ?? $config['filepath'] ?? null;
        return new JsonFileDriver($filepath);
    }
}