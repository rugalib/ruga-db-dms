<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Json\Json;
use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * Store the library in a database table.
 */
class DbDriver implements LibraryDriverInterface
{
    private RowGatewayInterface $row;
    
    
    
    public function __construct(RowGatewayInterface $row)
    {
        $this->row = $row;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->row->offsetGet(self::ATTR_NAME);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->row->offsetSet(self::ATTR_NAME, $name);
        $this->row->save();
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getRemark(): string
    {
        return $this->row->offsetGet(self::ATTR_REMARK);
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setRemark(string $name)
    {
        $this->row->offsetSet(self::ATTR_REMARK, $name);
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