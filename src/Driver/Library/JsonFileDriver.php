<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Laminas\Json\Json;
use Ruga\Dms\Driver\LibraryDriverInterface;

/**
 * Store the library in a JSON file.
 * Example:
 * ```
 * CREATE TABLE `DmsLibrary` (
 * `id` INT(10) NOT NULL AUTO_INCREMENT,
 * `name` VARCHAR(190) NULL DEFAULT NULL,
 * `config` TEXT NULL DEFAULT NULL,
 * `remark` TEXT NULL DEFAULT NULL,
 * `created` DATETIME NULL DEFAULT NULL,
 * `createdBy` INT(10) NULL DEFAULT NULL,
 * `changed` DATETIME NULL DEFAULT NULL,
 * `changedBy` INT(10) NULL DEFAULT NULL,
 * PRIMARY KEY (`id`),
 * UNIQUE INDEX `DmsLibrary_name_unique` (`name`),
 * INDEX `fk_DmsLibrary_changedBy_idx` (`changedBy`),
 * INDEX `fk_DmsLibrary_createdBy_idx` (`createdBy`),
 * CONSTRAINT `fk_DmsLibrary_changedBy` FOREIGN KEY (`changedBy`) REFERENCES `User` (`id`) ON UPDATE RESTRICT ON DELETE
 * RESTRICT, CONSTRAINT `fk_DmsLibrary_createdBy` FOREIGN KEY (`createdBy`) REFERENCES `User` (`id`) ON UPDATE RESTRICT
 * ON DELETE RESTRICT
 * )
 * ;
 * ```
 */
class JsonFileDriver implements LibraryDriverInterface
{
    private string $filepath;
    private array $data;
    
    
    
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->readFileIntoVar();
    }
    
    
    
    /**
     * Read and decode the JSON file into local data variable.
     *
     * @return void
     */
    private function readFileIntoVar()
    {
        if (file_exists($this->filepath)) {
            $this->data = Json::decode(file_get_contents($this->filepath), Json::TYPE_ARRAY);
        } else {
            $this->data = [];
        }
    }
    
    
    
    /**
     * Encode and save local variable to JSON file.
     *
     * @return void
     */
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
        $config['driver'] = self::class;
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