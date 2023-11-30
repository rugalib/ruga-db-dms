<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms;

use Ruga\Db\Schema\Updater;

/**
 * ConfigProvider.
 *
 * @see    https://docs.mezzio.dev/mezzio/v3/features/container/config/
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            Dms::class => [
                'default' => [
                    'name' => 'Default library',
                ],
            ],
            'db' => [
                Updater::class => [
                    'components' => [
                        Dms::class => [
                            Updater::CONF_REQUESTED_VERSION => 3,
                            Updater::CONF_SCHEMA_DIRECTORY => __DIR__ . '/../ruga-dbschema-dms',
                            Updater::CONF_TABLES => [
                                'DocumentTable' => \Ruga\Dms\Model\DocumentTable::class,
                                'LinkTable' => \Ruga\Dms\Model\LinkTable::class,
//                                'LibraryTable' => \Ruga\Dms\Library\LibraryTable::class,
                            ],
                        ],
                    ],
                ],
            ],
            'dependencies' => [
                'services' => [],
                'aliases' => [],
                'factories' => [
                    Library\LibraryInterface::class => Library\LibraryFactory::class,
                    Driver\Library\MemoryDriver::class => Driver\Library\MemoryDriverFactory::class,
                    Driver\Library\JsonFileDriver::class => Driver\Library\JsonFileDriverFactory::class,
                    Driver\Library\DbDriver::class => Driver\Library\DbDriverFactory::class,
                ],
                'invokables' => [],
                'delegators' => [],
            ],
        ];
    }
}
