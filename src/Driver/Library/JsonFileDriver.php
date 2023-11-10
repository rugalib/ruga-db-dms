<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Library;

use Laminas\Json\Json;
use Ruga\Dms\Library\Library;

class JsonFileDriver implements JsonFileDriverInterface
{
    private string $filepath;
    private array $data;
    
    
    
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }
    
    
    
    private function readFileIntoVar()
    {
        if (file_exists($this->filepath)) {
            $this->data = Json::decode(file_get_contents($this->filepath), Json::TYPE_ARRAY);
        } else {
            $this->data = [];
        }
    }
    
    
    
    private function saveVarInFile()
    {
        file_put_contents($this->filepath, Json::encode($this->data, true, ['prettyPrint' => true]));
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        $this->readFileIntoVar();
        return $this->data[self::ATTR_NAME];
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->readFileIntoVar();
        $this->data[self::ATTR_NAME] = $name;
        $this->saveVarInFile();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->saveVarInFile();
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function dumpConfig(): array
    {
        $config = [];
        $config['driver'] = JsonFileDriverInterface::class;
        $config['filepath'] = $this->filepath;
        return $config;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function setConfig(array $config)
    {
        $this->data = $config;
    }
}