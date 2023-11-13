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
    
    
    
    public function __construct(LibraryDriverInterface $libraryDriver, array $options = [])
    {
        $this->libraryDriver = $libraryDriver;
        
        $metaDriverName = $options[Library::CONFIG_METASTORAGE]['driver'] ?? \Ruga\Dms\Driver\Meta\MemoryDriver::class;
        if (is_a($metaDriverName, MetaDriverInterface::class, true)) {
            $this->metaDriver = new $metaDriverName($options[Library::CONFIG_METASTORAGE] ?? []);
        } else {
            throw new \InvalidArgumentException("'{$metaDriverName}' is not a valid MetaDriverInterface");
        }
        
        $dataDriverName = $options[Library::CONFIG_DATASTORAGE]['driver'] ?? \Ruga\Dms\Driver\Data\MemoryDriver::class;
        if (is_a($dataDriverName, DataDriverInterface::class, true)) {
            $this->dataDriver = new $dataDriverName($options[Library::CONFIG_DATASTORAGE] ?? []);
        } else {
            throw new \InvalidArgumentException("'{$dataDriverName}' is not a valid DataDriverInterface");
        }
        
        $linkDriverName = $options[Library::CONFIG_LINKSTORAGE]['driver'] ?? \Ruga\Dms\Driver\Link\MemoryDriver::class;
        if (is_a($linkDriverName, LinkDriverInterface::class, true)) {
            $this->linkDriver = new $linkDriverName($options[Library::CONFIG_LINKSTORAGE] ?? []);
        } else {
            throw new \InvalidArgumentException("'{$linkDriverName}' is not a valid LinkDriverInterface");
        }


//        $this->metaDriver = new \Ruga\Dms\Driver\Meta\MemoryDriver();
//        $this->dataDriver = new \Ruga\Dms\Driver\Data\MemoryDriver();
//        $this->linkDriver = new \Ruga\Dms\Driver\Link\MemoryDriver();

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
//        $this->dataDriver->save();
        $this->metaDriver->save();
//        $this->linkDriver->save();
        
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
