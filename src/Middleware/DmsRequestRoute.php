<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Middleware;

use Ruga\Std\Enum\AbstractEnum;
use Ruga\Std\Enum\EnumInterface;

/**
 * @method static self DOWNLOAD()
 * @method static self LIST()
 * @method static self DELETE()
 * @method static self UNKNOWN()
 */
class DmsRequestRoute extends AbstractEnum implements EnumInterface
{
    const DOWNLOAD = 'DOWNLOAD';
    const LIST = 'LIST';
    const DELETE = 'DELETE';
    const UNKNOWN = 'UNKNOWN';
}