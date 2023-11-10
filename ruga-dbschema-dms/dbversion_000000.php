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

return /** @lang MySQL */
    <<<"SQL"

SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `{$libraryTable}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(190) NULL,
  `config` TEXT NULL,
  `remark` TEXT NULL,
  `created` DATETIME NULL,
  `createdBy` INT NULL,
  `changed` DATETIME NULL,
  `changedBy` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE `{$libraryTable}_name_unique` (`name`),
  INDEX `fk_{$libraryTable}_changedBy_idx` (`changedBy` ASC),
  INDEX `fk_{$libraryTable}_createdBy_idx` (`createdBy` ASC),
  CONSTRAINT `fk_{$libraryTable}_changedBy` FOREIGN KEY (`changedBy`) REFERENCES `{$userTable}` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_{$libraryTable}_createdBy` FOREIGN KEY (`createdBy`) REFERENCES `{$userTable}` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
;
SET FOREIGN_KEY_CHECKS = 1;

SQL;
