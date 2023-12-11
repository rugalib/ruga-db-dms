<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Ruga\Dms\Driver\LibraryDriverInterface;
use Ruga\Dms\Dms;

class LibraryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LibraryInterface
    {
        /** @var ServiceManager $container */
        $config = $container->get('config')[Dms::class][$options['name'] ?? 'default'] ?? $container->get(
            'config'
        )[Dms::class] ?? [];
        
        $options = $options ?? $config;
        if (!empty($options['name'] ?? null)) {
            $options[Library::CONFIG_LIBRARYSTORAGE]['name'] = $options['name'];
        }
        
        
        if ($options[Library::CONFIG_LIBRARYSTORAGE]['driver'] ?? false) {
            /** @var LibraryDriverInterface $libraryDriver */
            $libraryDriver = $container->build(
                $options[Library::CONFIG_LIBRARYSTORAGE]['driver'],
                $options[Library::CONFIG_LIBRARYSTORAGE]
            );
        } else {
            /** @var LibraryDriverInterface $libraryDriver */
            $libraryDriver = $container->get(LibraryDriverInterface::class);
        }
        
        
        $library = new Library($libraryDriver, $options);

//        $library->setName($options['name']);
        $library->save();
        
        
        /*
        if ($config[Library::CONFIG_METASTORAGE]['driver'] == 'db') {
            $adapterInConfig = $config[Library::CONFIG_METASTORAGE]['adapter'] ?? null;
            if ($adapterInConfig instanceof AdapterInterface) {
            } elseif (is_string($adapterInConfig)) {
                $config[Library::CONFIG_METASTORAGE]['adapter'] = $container->get($adapterInConfig);
            } else {
                throw new DbAdapterMissingException("Must provide a name of a database adapter or a adapter instance in the key 'adapter'.");
            }
        }
        */
        
        return $library;
    }
}



