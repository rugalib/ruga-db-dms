<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Library;

use Laminas\Json\Json;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Ruga\Dms\Dms;
use Ruga\Dms\Library\Library;

class JsonFileDriverFactory implements FactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config')[Dms::class][Library::CONFIG_LIBRARYSTORAGE] ?? [];
        $filepath = $options['filepath'] ?? $config['filepath'] ?? null;
        if (empty($filepath)) {
            throw new \InvalidArgumentException('filepath is required in configuration');
        }
        
        $libraryName = $options['name'] ?? (Uuid::uuid5(
            Uuid::NAMESPACE_OID,
            hash('sha256', uniqid(date('U'), true))
        ))->toString();
        
        if (!file_exists($filepath)) {
            file_put_contents($filepath, Json::encode(['name' => $libraryName], true, ['prettyPrint' => true]));
        }
        $filepath = realpath($filepath);
        return new JsonFileDriver($filepath, $options);
    }
}