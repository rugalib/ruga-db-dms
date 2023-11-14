<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Model;

use Ruga\Db\Table\AbstractTable;

/**
 * The document table.
 */
class LinkTable extends AbstractTable
{
    const TABLENAME = 'DmsLink';
    const PRIMARYKEY = ['id'];
    const ROWCLASS = Link::class;
    
    
}
