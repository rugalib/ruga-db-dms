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
$person = $resolver->getTableName(\Ruga\Person\PersonTable::class);

return /** @lang MySQL */
    <<<"SQL"

SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `{$person}` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(190) NULL,
  `salutation` VARCHAR(190) NULL DEFAULT NULL,
  `first_name` VARCHAR(190) NULL DEFAULT NULL,
  `title` VARCHAR(190) NULL DEFAULT NULL,
  `prefix` VARCHAR(190) NULL DEFAULT NULL,
  `last_name` VARCHAR(190) NULL DEFAULT NULL,
  `middle_name` VARCHAR(190) NULL DEFAULT NULL,
  `birth_name` VARCHAR(190) NULL DEFAULT NULL,
  `religious_name` VARCHAR(190) NULL DEFAULT NULL,
  `nickname` VARCHAR(190) NULL DEFAULT NULL,
  `gender` VARCHAR(3) NULL DEFAULT NULL,
  `nationality` VARCHAR(3) NULL DEFAULT NULL,
  `language` VARCHAR(3) NULL DEFAULT NULL,
  `date_of_birth` DATE NULL DEFAULT NULL,
  `date_of_death` DATE NULL DEFAULT NULL,
  `remark` TEXT NULL,
  `created` DATETIME NULL,
  `createdBy` INT NULL,
  `changed` DATETIME NULL,
  `changedBy` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `{$person}_fullname_idx` (`fullname`),
  INDEX `fk_{$person}_changedBy_idx` (`changedBy` ASC),
  INDEX `fk_{$person}_createdBy_idx` (`createdBy` ASC),
  CONSTRAINT `fk_{$person}_changedBy` FOREIGN KEY (`changedBy`) REFERENCES `User` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_{$person}_createdBy` FOREIGN KEY (`createdBy`) REFERENCES `User` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
;
SET FOREIGN_KEY_CHECKS = 1;

SQL;
