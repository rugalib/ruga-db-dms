<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);


namespace Ruga\Dms\Driver\Library;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class MemoryDriverFactory implements FactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $libraryName = $options['name'] ?? (Uuid::uuid5(
            Uuid::NAMESPACE_OID,
            hash('sha256', uniqid(date('U'), true))
        ))->toString();
        
        return new MemoryDriver($libraryName);
    }
}