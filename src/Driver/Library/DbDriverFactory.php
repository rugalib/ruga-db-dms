<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Driver\Library;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Ruga\Db\Adapter\AdapterInterface;
use Ruga\Dms\Driver\LibraryDriverInterface;

class DbDriverFactory implements FactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
//        $config = $container->get('config')[Dms::class][Library::CONFIG_LIBRARYSTORAGE] ?? [];
        
        if (($options['adapter'] ?? '') instanceof AdapterInterface) {
            $adapter = $options['adapter'];
        } else {
            $dbadaptername = $options['adapter'] ?? $config['adapter'] ?? AdapterInterface::class;
            $adapter = $container->get($dbadaptername);
        }
        
        $libraryName = $options['name'] ?? (Uuid::uuid5(
            Uuid::NAMESPACE_OID,
            hash('sha256', uniqid(date('U'), true))
        ))->toString();
        
        $tableName = 'DmsLibrary';
        
        $table = new TableGateway(
            $tableName,
            $adapter,
            null,
            (new ResultSet())->setArrayObjectPrototype(new RowGateway('id', $tableName, $adapter))
        );
        $sql = $table->getSql();
        $select = $sql->select()
            ->where([LibraryDriverInterface::ATTR_NAME => $libraryName]);
        if (!$row = $table->selectWith($select)->current()) {
            $row = new RowGateway('id', $tableName, $adapter);
            $row->offsetSet(LibraryDriverInterface::ATTR_NAME, $libraryName);
        }
        
        
        return new DbDriver($row, $options);
    }
}