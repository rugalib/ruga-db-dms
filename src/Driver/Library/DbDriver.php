<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Laminas\Db\RowGateway\AbstractRowGateway;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Sql;
use Laminas\Json\Json;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * Store the library in a database table.
 */
class DbDriver implements LibraryDriverInterface
{
    private AdapterInterface $adapter;
    private AbstractRowGateway $row;
    private string $name;
    private array $data;
    
    
    
    public function __construct(AdapterInterface $adapter, string $tableName='DmsLibrary')
    {
        $this->adapter = $adapter;
        $this->name = 'name';
        
        $sql = new Sql($this->adapter);
        $select = $sql->select()
            ->from($tableName)
            ->where(['name' => $this->name]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        if (!$row = $result->current()) {
            $row = new RowGateway('id', $tableName, $this->adapter);
        }
        $this->row = $row;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->row->offsetGet('name');
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->row->offsetSet('name', $name);
        $this->row->save();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = self::class;
        return $config;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setConfig(array $config)
    {
        $this->row->offsetSet('config', Json::encode($config, true, ['prettyPrint' => true]));
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->row->save();
    }
}