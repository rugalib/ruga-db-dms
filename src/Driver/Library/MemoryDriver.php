<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * Store the library in memory.
 */
class MemoryDriver implements LibraryDriverInterface
{
    private string $name;
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
        // Not applicable
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        // Not applicable
    }
}