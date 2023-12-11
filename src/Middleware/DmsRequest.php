<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DmsRequest
{
    private ServerRequestInterface $request;
    
    
    
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    
    
    /**
     * Returns the original request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
    
    
    
    /**
     * Return an array containing all the path components.
     *
     * @return array
     */
    public function getRequestPathParts(): array
    {
        $uriPath = trim($this->request->getUri()->getPath(), " /\\");
        return explode('/', $uriPath);
    }
    
    
    
    /**
     * Return the name of the requested library.
     *
     * @return string
     */
    public function getLibraryName(): string
    {
        return $this->getRequestPathParts()[0] ?? '';
    }
    
    
    
    /**
     * Return the command from the requested path.
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->getRequestPathParts()[1] ?? '';
    }
    
    
    
    /**
     * Returns the value of the key parameter from the query string or parsed body of the current request.
     *
     * @return string|null The value of the key parameter if it exists, null otherwise.
     */
    public function getKey(): ?string
    {
        return $this->getRequest()->getQueryParams()['key'] ?? $this->getRequest()->getParsedBody()['key'] ?? null;
    }
    
    
    
    /**
     * Return the category value of the current request. It can be either a single string value or an array of string
     * values. If the category value is a comma-separated string, it will be split into an array of string values.
     *
     * @return array|null Returns the category value as an array of string values if it is a comma-separated string, or
     *                    a single string value if it is not. Returns null if the category value is not present in the
     *                    request query parameters.
     */
    public function getCategory(): ?array
    {
        $cat = $this->getRequest()->getQueryParams()['category'] ?? null;
        if (is_string($cat)) {
            if (strpos($cat, ',')) {
                return explode(',', $cat);
            }
            return [$cat];
        }
        return $cat;
    }
    
    
    
    /**
     * Return the UUID of the requested document.
     *
     * @return UuidInterface
     */
    public function getDocumentUuid(): UuidInterface
    {
        try {
            $uuid = Uuid::fromString($this->getRequestPathParts()[2] ?? '');
        } catch (\Throwable $e) {
            try {
                $uuid = Uuid::fromString($this->getRequestPathParts()[1] ?? '');
            } catch (\Throwable $e) {
                $uuid = Uuid::fromString($this->getRequestPathParts()[0] ?? '');
            }
        }
        return $uuid;
    }
    
    
    
    /**
     * Return the base URL of the requested library.
     *
     * @return UriInterface The base URL of the requested library.
     */
    public function getBaseUrl(): UriInterface
    {
        $url = new Uri($this->getRequest()->getAttribute('_base_url'));
        return $url;
    }
    
    
    
    /**
     * Return the route by analyzing the request.
     *
     * @return DmsRequestRoute
     */
    public function getRequestRoute(): DmsRequestRoute
    {
        if (in_array(
                $this->getRequest()->getMethod(),
                [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_HEAD]
            )
            && ($this->getCommand() == 'download')) {
            return DmsRequestRoute::DOWNLOAD();
        }
        
        if (in_array(
                $this->getRequest()->getMethod(),
                [RequestMethodInterface::METHOD_GET]
            )
            && ($this->getCommand() == 'list')) {
            return DmsRequestRoute::LIST();
        }
        
        if (in_array(
            $this->getRequest()->getMethod(),
            [RequestMethodInterface::METHOD_DELETE]
        )) {
            return DmsRequestRoute::DELETE();
        }
        
        
        return DmsRequestRoute::UNKNOWN();
    }
}