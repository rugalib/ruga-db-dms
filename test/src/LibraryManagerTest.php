<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test;

use Laminas\ConfigAggregator\ConfigAggregator;
use Ruga\Dms\Dms;
use Ruga\Dms\Library\Exception\InvalidLibraryNameException;
use Ruga\Dms\Library\LibraryInterface;
use Ruga\Dms\Library\LibraryManager;

/**
 * @author                 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class LibraryManagerTest extends \Ruga\Dms\Test\PHPUnit\AbstractTestSetUp
{
    
    private array $config = [
        \Ruga\Dms\Dms::class => [
            \Ruga\Dms\Dms::CONF_LIBRARY_MANAGER => [
                'helloworld' => [
                    'name' => 'Hello World',
                    \Ruga\Dms\Library\Library::CONFIG_LIBRARYSTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Library\JsonFileDriver::class,
                        'filepath' => __DIR__ . '/../data/libraries/hw.json',
                    ],
                    \Ruga\Dms\Library\Library::CONFIG_DATASTORAGE => [
                        'driver' => \Ruga\Dms\Driver\Data\ObjectstorageDriver::class,
                        'basepath' => __DIR__ . '/../data/files',
                    ],
                ],
            ],
        ],
    ];
    
    
    
    public function configProvider(array $additionalConfig = [])
    {
        $config = new ConfigAggregator([
                                           function () {
                                               return parent::configProvider();
                                           },
                                           function () use ($additionalConfig) {
                                               return $additionalConfig;
                                           },
                                       ]);
        return $config->getMergedConfig();
    }
    
    
    
    /**
     * Test case for creating new library from name which is not exist in configuration.
     */
    public function testCreateLibraryFromNonexistentName(): void
    {
        $libraryManager = new LibraryManager(
            $this->getContainer(),
            $this->configProvider($this->config)[Dms::class][Dms::CONF_LIBRARY_MANAGER]
        );
        $this->expectException(InvalidLibraryNameException::class);
        $library_name_that_not_exist = 'libraryX';
        $libraryManager->createLibraryFromName($library_name_that_not_exist);
    }
    
    
    
    /**
     * Test case for creating new library from name which exists in configuration.
     */
    public function testCreateLibraryFromExistingName(): void
    {
        $libraryManager = new LibraryManager(
            $this->getContainer(),
            $this->configProvider($this->config)[Dms::class][Dms::CONF_LIBRARY_MANAGER]
        );
        $existing_library_name = 'helloworld';
        $library = $libraryManager->createLibraryFromName($existing_library_name);
        $this->assertInstanceOf(LibraryInterface::class, $library);
    }
    
    
    
    /**
     * Test case for creating new library and checking its name.
     *
     * @return void
     */
    public function testCreateLibraryAndCheckName(): void
    {
        $libraryManager = new LibraryManager(
            $this->getContainer(),
            $this->configProvider($this->config)[Dms::class][Dms::CONF_LIBRARY_MANAGER]
        );
        $library_name = 'helloworld';
        $library = $libraryManager->createLibraryFromName($library_name);
        $this->assertEquals($library_name, $library->getName());
    }
    
}
