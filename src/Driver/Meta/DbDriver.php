<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Meta;

use Ramsey\Uuid\UuidInterface;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Db\Table\AbstractTable;
use Ruga\Dms\Document\AbstractDocument;
use Ruga\Dms\Driver\Meta\StorageContainer\DbStorageContainer;
use Ruga\Dms\Driver\MetaDriverInterface;
use Ruga\Dms\Driver\MetaStorageContainerInterface;
use Ruga\Dms\Library\LibraryInterface;
use Ruga\Dms\Model\DocumentTable;

class DbDriver extends AbstractDriver implements MetaDriverInterface
{
    private AbstractTable $table;
    private \SplObjectStorage $storage;
    
    
    
    public function __construct(LibraryInterface $library, array $config)
    {
        parent::__construct($library);
        
        $this->storage = new \SplObjectStorage();
        
        if (($config['table'] ?? '') instanceof AbstractTable) {
            $this->table = $config['table'];
            return;
        }
        if ($config['adapter'] instanceof AdapterInterface) {
            $tableclassname = $config['table'] ?? DocumentTable::class;
            $this->table = new $tableclassname($config['adapter']);
            return;
        }
        throw new \InvalidArgumentException(
            "'table' must be a \Ruga\Db\Table\AbstractTable instance or 'adapter' must be a \Ruga\Db\Adapter\AdapterInterface instance"
        );
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function createStorage(): MetaStorageContainerInterface
    {
        $container = new DbStorageContainer($this, $this->table->createRow());
        $this->storage->attach($container, $container->getUuid());
        return $container;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function findByUuid($uuid): \ArrayIterator
    {
        if ($uuid instanceof UuidInterface) {
            $uuid = $uuid->toString();
        }
        $rs = $this->table->select(['uuid' => $uuid]);
        $a = [];
        /** @var AbstractDocument $r */
        foreach ($rs as $r) {
            $container = new DbStorageContainer($this, $r);
            
            $a[] = $container;
        }
        return new \ArrayIterator($a);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = \Ruga\Dms\Driver\Meta\DbDriver::class;
        $config['table'] = $this->table->getTable();
        $config['adapter'] = get_class($this->table->getAdapter());
        return $config;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        /** @var MetaStorageContainerInterface $item */
        foreach ($this->storage as $item) {
            $a[] = $item->save();
        }
    }
    
    
}