<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Document;

use Ruga\Std\Enum\AbstractEnum;

/**
 * Defines the types of communication mechanisms available.
 *
 * @method static self GENERIC()
 * @method static self ICON()
 * @method static self IMAGE()
 * @method static self THUMBNAIL()
 * @method static self DATASHEET()
 * @method static self DOWNLOAD()
 * @method static self CONFIG()
 * @method static self DOCUMENT()
 * @method static self COPY()
 * @method static self AGREEMENT()
 */
class DocumentType extends AbstractEnum
{
    const GENERIC = 'GENERIC';
    const ICON = 'ICON';
    const IMAGE = 'IMAGE';
    const THUMBNAIL = 'THUMBNAIL';
    const DATASHEET = 'DATASHEET';
    const DOWNLOAD = 'DOWNLOAD';
    const CONFIG = 'CONFIG';
    const DOCUMENT = 'DOCUMENT';
    const COPY = 'COPY';
    const AGREEMENT = 'AGREEMENT';
    
    const __fullnameMap = [
        self::GENERIC => 'Generisch',
        self::ICON => 'Icon',
        self::IMAGE => 'Bild',
        self::THUMBNAIL => 'Voransicht',
        self::DATASHEET => 'Datenblatt',
        self::DOWNLOAD => 'Download',
        self::CONFIG => 'Konfiguration',
        self::DOCUMENT => 'Dokument',
        self::COPY => 'Kopie',
        self::AGREEMENT => 'Vertrag',
    ];
}

