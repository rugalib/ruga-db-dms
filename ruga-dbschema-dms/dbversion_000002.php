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
$tableDocument = $resolver->getTableName(\Ruga\Dms\Model\DocumentTable::class);
$linkTable = $resolver->getTableName(\Ruga\Dms\Model\LinkTable::class);

return /** @lang MySQL */
    <<<"SQL"
SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `{$linkTable}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Foreign_key` VARCHAR(255),
  `Foreign_uuid` VARCHAR(36),
  `Meta_uuid` VARCHAR(36),
  `remark` TEXT NULL DEFAULT NULL,

  PRIMARY KEY (`id` ASC),
  UNIQUE INDEX `{$tableDocument}_uuid_UNIQUE` (`Foreign_uuid`, `Meta_uuid`),
  INDEX `{$tableDocument}_Foreign_uuid_idx` (`Foreign_uuid`),
  INDEX `{$tableDocument}_Document_uuid_idx` (`Meta_uuid`),

  CONSTRAINT `fk_{$linkTable}_Document_uuid` FOREIGN KEY (`Meta_uuid`) REFERENCES `{$tableDocument}` (`uuid`) ON UPDATE RESTRICT ON DELETE RESTRICT
)
ENGINE = InnoDB
;
SET FOREIGN_KEY_CHECKS = 1;

SQL;
