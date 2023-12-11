<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;

use Psr\Container\ContainerInterface;
use Ruga\Dms\Library\Exception\InvalidLibraryNameException;

class LibraryManager
{
    private ContainerInterface $container;
    private array $config;
    
    /**
     * @var LibraryInterface[]
     */
    private array $cache = [];
    
    
    
    public function __construct(ContainerInterface $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
    }
    
    
    
    /**
     * Creates a library object based on the provided name.
     *
     * @param string $name The name of the library to create.
     *
     * @return LibraryInterface The library object.
     * @throws InvalidLibraryNameException If the library with the specified name does not exist in the configuration.
     *
     */
    public function createLibraryFromName(string $name)
    {
        if (!array_key_exists($name, $this->config)) {
            throw new InvalidLibraryNameException("Library with name '{$name}' does not exist in configuration");
        }
        
        $config = $this->config[$name];
        
        if (!array_key_exists($name, $this->cache)) {
            $factoryName = $config['factory'] ?? '';
            if (class_exists($factoryName)) {
                $this->cache[$name] = (new $factoryName())($this->container, $name, $config);
            } else {
                $this->cache[$name] = $this->container->build(LibraryInterface::class, $this->config[$name]);
            }
            $this->cache[$name]->setName($name);
        }
        return $this->cache[$name];
    }
}
