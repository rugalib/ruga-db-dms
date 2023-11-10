<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Model;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Ruga\Db\Row\RowInterface;
use Ruga\Db\Table\AbstractRugaTable;

/**
 * The document table.
 */
class DocumentTable extends AbstractRugaTable
{
    const TABLENAME = 'DmsDocument';
    const PRIMARYKEY = ['id'];
    const ROWCLASS = Document::class;
    
    
    
    /**
     * Find documents by linked entity and by category.
     *
     * @param RowInterface $row
     * @param null         $categories
     *
     * @return ResultSet
     * @throws \Exception
     */
    public function findByObject(RowInterface $row, $categories = null): \Laminas\Db\ResultSet\ResultSet
    {
        \Ruga\Log::functionHead();
        
        $leftTableName = $row->getTableGateway()->getTable();
        $rightTableName = $this->getTable();
        $linkTableDbName = "{$leftTableName}_has_{$rightTableName}";
        $linkTableClassName = (new \ReflectionClass($row))->getShortName() . "Has" . self::ROWCLASS;
        \Ruga\Log::log_msg("Link table db name: {$linkTableDbName}");
        \Ruga\Log::log_msg("Link table class name: {$linkTableClassName}");

//        $linkTable=$this->getTableGateway()->getAdapter()->tableFactory($linkTableClassName);
        
        $primaryKey = ["{$leftTableName}_id", "{$rightTableName}_id"];
        
        $linkTable = new GenericLinkTable($linkTableDbName, $this->getAdapter(), $primaryKey);
        \Ruga\Log::log_msg("Link table class name: " . get_class($linkTable));
        
        /** @var Select $sql */
        $sql = $linkTable->getSql()->select();
        $sql->join(['r' => $rightTableName], "r.id={$linkTableDbName}.{$primaryKey[1]}", []);
        $sql->where(function (Where $where) use ($linkTableDbName, $primaryKey, $row, $categories) {
            $where->equalTo("{$linkTableDbName}.{$primaryKey[0]}", $row->PK);
            if (!empty($categories)) {
                $where->in("r.category", (array)$categories);
            }
        });
        
        \Ruga\Log::log_msg("SQL={$sql->getSqlString($linkTable->getAdapter()->getPlatform())}");
        $links = $linkTable->selectWith($sql);
        
        $documentIds = array_map(
            function (RowGatewayInterface $link) use ($primaryKey) {
                return $link->{$primaryKey[1]};
            },
            iterator_to_array($links)
        );
        
        return $this->findById($documentIds);
    }
}
