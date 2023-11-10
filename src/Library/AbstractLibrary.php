<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Library;


use Ruga\Dms\Driver\DataDriverInterface;
use Ruga\Dms\Driver\LibraryDriverInterface;
use Ruga\Dms\Driver\LinkDriverInterface;
use Ruga\Dms\Driver\MetaDriverInterface;

/**
 * Abstract template.
 */
abstract class AbstractLibrary implements LibraryInterface
{
    protected LibraryDriverInterface $libraryDriver;
    protected MetaDriverInterface $metaDriver;
    protected DataDriverInterface $dataDriver;
    protected LinkDriverInterface $linkDriver;
    
    
    
    public function __construct(LibraryDriverInterface $libraryDriver)
    {
        $this->libraryDriver = $libraryDriver;
        
        
        $this->metaDriver = new \Ruga\Dms\Driver\Meta\MemoryDriver();
        $this->dataDriver = new \Ruga\Dms\Driver\Data\MemoryDriver();
        $this->linkDriver = new \Ruga\Dms\Driver\Link\MemoryDriver();

//        $this->metaAdapter = MetaAdapterFactory::factory($config[self::CONFIG_METASTORAGE] ?? []);
//        $this->dataAdapter = DataAdapterFactory::factory($config[self::CONFIG_DATASTORAGE] ?? []);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->libraryDriver->getName();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setName(string $name)
    {
        $this->libraryDriver->setName($name);
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        $config = $this->dumpConfig();
        $this->libraryDriver->setConfig($config);
        $this->libraryDriver->save();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function dumpConfig(): array
    {
        $config = ['name' => $this->getName()];
        $config[Library::CONFIG_LIBRARYSTORAGE] = $this->libraryDriver->dumpConfig();
        $config[Library::CONFIG_METASTORAGE] = $this->metaDriver->dumpConfig();
        $config[Library::CONFIG_DATASTORAGE] = $this->dataDriver->dumpConfig();
        $config[Library::CONFIG_LINKSTORAGE] = $this->linkDriver->dumpConfig();
        return $config;
    }
    
}
