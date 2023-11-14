<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

/**
 * @return string
 * @var \Ruga\Db\Schema\Resolver $resolver
 * @var string                   $comp_name
 */
$userTable = 'User';
//$libraryTable = $resolver->getTableName(\Ruga\Dms\Model\LibraryTable::class);
$libraryTable = 'DmsLibrary';
$documentTable = $resolver->getTableName(\Ruga\Dms\Model\DocumentTable::class);
if ($document_type_values = implode("','", \Ruga\Dms\Document\DocumentType::getConstants())) {
    $document_type_values = "'{$document_type_values}'";
}

return /** @lang MySQL */
    <<<"SQL"
SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `{$documentTable}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) DEFAULT NULL,
  `name` VARCHAR(255),
  `uuid` VARCHAR(36),
  `library` VARCHAR(255),
  `category` VARCHAR(255) DEFAULT NULL,
  `document_type` ENUM({$document_type_values}) NOT NULL DEFAULT 'GENERIC',
  `mimetype` VARCHAR(255) DEFAULT NULL,
  `lastmodified` DATETIME DEFAULT NULL,
  `filename` VARCHAR(255) DEFAULT NULL,
  `priority` INT NOT NULL DEFAULT '0',
  `datapath` VARCHAR(255) DEFAULT NULL,
  `datahash` VARCHAR(64) DEFAULT NULL,
  `metadata` TEXT,
  
  `remark` TEXT NULL DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `createdBy` INT DEFAULT '0',
  `changed` DATETIME NOT NULL,
  `changedBy` INT DEFAULT '0',

  PRIMARY KEY (`id` ASC),
  INDEX `{$documentTable}_fullname_idx` (`fullname`),
  UNIQUE INDEX `{$documentTable}_datapath_UNIQUE` (`datapath`),
  INDEX `{$documentTable}_document_type_idx` (`document_type`),
  INDEX `{$documentTable}_priority_idx` (`priority`),
  INDEX `{$documentTable}_mimetype_idx` (`mimetype`),
  INDEX `{$documentTable}_name_idx` (`name`),
  INDEX `{$documentTable}_uuid_idx` (`uuid`),
  INDEX `{$documentTable}_filepath_idx` (`filename`),
  INDEX `{$documentTable}_library_idx` (`library`),
  INDEX `{$documentTable}_category_idx` (`category`),

  INDEX `fk_{$documentTable}_changedBy_idx` (`changedBy`),
  INDEX `fk_{$documentTable}_createdBy_idx` (`createdBy`)
  # CONSTRAINT `fk_{$documentTable}_changedBy` FOREIGN KEY (`changedBy`) REFERENCES `{$userTable}` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  # CONSTRAINT `fk_{$documentTable}_createdBy` FOREIGN KEY (`createdBy`) REFERENCES `{$userTable}` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = InnoDB
;
SET FOREIGN_KEY_CHECKS = 1;

SQL;
