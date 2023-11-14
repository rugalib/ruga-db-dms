<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Link;

class LinkObject
{
    public string $foreignKey;
    public string $foreignUuid;
    public string $metaUuid;
    public ?string $remark = null;
    
    
    
    public function __construct(?string $foreignKey = null, ?string $foreignUuid = null, ?string $metaUuid = null)
    {
        if ($foreignKey !== null) {
            $this->foreignKey = $foreignKey;
        }
        if ($foreignUuid !== null) {
            $this->foreignUuid = $foreignUuid;
        }
        if ($metaUuid !== null) {
            $this->metaUuid = $metaUuid;
        }
    }
    
}