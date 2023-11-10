<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Model;

use Ruga\Db\Row\RowAttributesInterface;
use Ruga\Dms\Document\DocumentType;

/**
 * Interface DocumentAttributesInterface
 *
 * @see     Document
 * @see     AbstractDocument
 *
 * @property int          $id                        Primary Key
 * @property string       $fullname                  Full name / display name
 * @property string       $name                      FilesystemFile name
 * @property string       $library                   Storage library
 * @property string       $category                  Document category
 * @property DocumentType $document_type             DocumentType ('GENERIC','ICON','IMAGE', ...)
 * @property string       $mimetype                  MIME type
 * @property string       $filepath                  Filename and path the document has in the application
 * @property int          $priority                  Priority if more than one of the same mediatype
 * @property string       $data_unique_key           Unique key to the content data store (for file based storage it's
 *           the file name and path)
 * @property string       $hash                      SHA256 hash
 * @property string       $metadata                  Serialized meta data
 */
interface DocumentAttributesInterface extends RowAttributesInterface
{
    
}