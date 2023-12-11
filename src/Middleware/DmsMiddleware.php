<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ruga\Dms\Document\DocumentInterface;
use Ruga\Dms\Library\Exception\InvalidLibarayNameException;
use Ruga\Dms\Library\LibraryInterface;
use Ruga\Dms\Library\LibraryManager;


/**
 * DmsMiddleware creates a DmsRequest from a serverSide request and tries to find the desired plugin.
 * If found, the process method is executed and returns a DmsResponse, which is returned to the client.
 *
 * @see     DmsMiddlewareFactory
 */
class DmsMiddleware implements MiddlewareInterface
{
    private LibraryManager $libraryManager;
    private LibraryInterface $library;
    
    
    
    public function __construct(LibraryManager $libraryManager)
    {
        $this->libraryManager = $libraryManager;
    }
    
    
    
    /**
     * Process an incoming server request.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        try {
            $dmsRequest = new DmsRequest($request);
            
            $libraryName = $dmsRequest->getLibraryName();
            try {
                $this->library = $this->libraryManager->createLibraryFromName($libraryName);
            } catch (InvalidLibarayNameException $e) {
                throw new InvalidLibarayNameException($e->getMessage(), StatusCodeInterface::STATUS_NOT_FOUND, $e);
            }
            
            
            switch ($dmsRequest->getRequestRoute()) {
                case DmsRequestRoute::LIST():
                    return $this->processListRequest($dmsRequest);
                
                case DmsRequestRoute::DOWNLOAD():
                    return $this->processDownloadRequest($dmsRequest);
                
                case DmsRequestRoute::DELETE():
//                    return $this->processDeleteRequest($dmsRequest);
            }
            
            
            throw new \Exception("Request not implemented", StatusCodeInterface::STATUS_NOT_IMPLEMENTED);
        } catch (\Throwable $e) {
            \Ruga\Log::addLog($e);
            $status = (($e->getCode() >= 400) && ($e->getCode() < 600)) ? $e->getCode(
            ) : StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            return new TextResponse($e->getMessage(), $status);
        }
    }
    
    
    
    /**
     * Format a file size in bytes to a human-readable format.
     *
     * @param int $fileSize The size of the file in bytes.
     * @param int $decimal  The number of decimal places to round to (default: 2).
     *
     * @return string The formatted file size in a human-readable format.
     * @deprecated
     */
    public function format_filesize(int $fileSize, int $decimal = 2): string
    {
        $S = 'kMGTPEZY';
        $F = floor((strlen(strval($fileSize)) - 1) / 3);
        return sprintf("%.{$decimal}f", $fileSize / pow(1024, $F)) . @$S[$F - 1] . 'B';
    }
    
    
    
    /**
     * Process a list request.
     *
     * @param DmsRequest $request The request object.
     *
     * @return ResponseInterface The response object.
     * @throws \InvalidArgumentException If 'key' parameter is empty.
     */
    private function processListRequest(DmsRequest $request): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        if (!$key = $request->getKey()) {
            throw new \InvalidArgumentException("Parameter 'key' is empty");
        }
        $category = $request->getCategory();
        \Ruga\Log::addLog("category=" . print_r($category, true));
        $documents = $this->library->findDocumentsByForeignKey($key, $category);
        
        $baseUrl = $request->getBaseUrl();
        
        $str = '<ul data-dms-library-name="' . $this->library->getName() . '" data-dms-key="' . $key . '">';
        /** @var DocumentInterface $document */
        foreach ($documents as $document) {
            $str .= '<li>';
            $str .= "<a href=\"{$document->getDownloadUri(strval($baseUrl))}\">";
            $str .= $document->getName();
            $str .= " ({$this->format_filesize($document->getContentLength(), 0)})";
            $str .= '</a>';
            $str .= '<button type="button" class="btn btn-outline-danger btn-xs" data-dms-command="delete" data-dms-document-uuid="' . $document->getUuid(
                )
                . '"><i class="fa-regular fa-trash-can"></i></button>';
            $str .= '</li>';
        }
        
        $str .= /** @lang HTML */
            <<< HTML
<script>
(function ($, window, document) {
    $(function () {
        $('button[data-dms-command=delete]').on('click', function(event) {
            // console.log('event=',event);
            // console.log('this=',this);
            $(this).closest('ul').parent().find("*").prop("disabled", true);
            $(this).html('<i class="fa-solid fa-spinner fa-spin"></i>');
            const url = $(this).siblings('a').attr('href');
            const key = $(this).closest('[data-dms-key]').data('dmsKey');
            const data = {
                key: key
            };
            // console.log('url=',url);
            // console.log('key=',key);
            $.ajax({
                url: url + '?' + $.param(data),
                type: 'DELETE',
                success: function(data, textStatus, jqXHR) {
                    $(this).closest('li').remove();
                }.bind(this),
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Fehler bei der DELETE-Anfrage:', errorThrown);
                    $(this).closest('ul').parent().trigger('reload');
                }.bind(this)
            });
        });
    });
}(window.jQuery, window, document));
</script>
HTML;
        
        
        $str .= '</ul>';
        
        return new HtmlResponse($str);
    }
    
    
    
    /**
     * Process a download request.
     *
     * @param DmsRequest $request The request object.
     *
     * @return ResponseInterface The response object.
     * @throws \InvalidArgumentException If no document is found by the provided uuid.
     */
    private function processDownloadRequest(DmsRequest $request): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        $documentUuid = $request->getDocumentUuid();
        /** @var DocumentInterface $doc */
        if (!$doc = $this->library->findDocumentsByUuid($documentUuid)->current()) {
            throw new \InvalidArgumentException(
                "No document found by uuid '{$documentUuid}'",
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        
        $headers = [
            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Length, Content-Type',
            'Content-Disposition' => 'attachment; filename="' . strval($doc->getFilename()) . '"',
            'Content-Length' => $doc->getContentLength(),
            'Content-Type' => $doc->getMimetype(),
            'Cache-Control' => ['max-age=0', 'max-age=1', 'cache, must-revalidate'],
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $doc->getLastModified()->getTimestamp()) . ' GMT',
            'Pragma' => 'public',
            'Set-Cookie' => 'fileDownload=true; path=/', // https://github.com/johnculviner/jquery.fileDownload
            'X-Dms-Document-Uuid' => $doc->getUuid()->toString(),
            'X-Dms-Document-Category' => strval($doc->getCategory()),
            'X-Dms-Library-Name' => $this->library->getName(),
        ];
        
        if ($request->getRequest()->getMethod() == RequestMethodInterface::METHOD_HEAD) {
            $response = new Response\EmptyResponse(StatusCodeInterface::STATUS_OK, $headers);
        } else {
            $response = new Response($doc->getContentStream(), StatusCodeInterface::STATUS_OK, $headers);
        }
        
        return $response;
    }
    
    
    
    /**
     * Process a delete request.
     *
     * @param DmsRequest $request The request object.
     *
     * @return ResponseInterface The response object.
     * @throws \InvalidArgumentException If 'key' parameter is empty or if no document is found by uuid.
     */
    private function processDeleteRequest(DmsRequest $request): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        if (!$key = $request->getKey()) {
            throw new \InvalidArgumentException("Parameter 'key' is empty");
        }
        
        $documentUuid = $request->getDocumentUuid();
        /** @var DocumentInterface $doc */
        if (!$doc = $this->library->findDocumentsByUuid($documentUuid)->current()) {
            throw new \InvalidArgumentException(
                "No document found by uuid '{$documentUuid}'",
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        
        $doc->unlinkFrom($key);
        $doc->save();
        
        // reload
        /** @var DocumentInterface $doc */
        if (!$doc = $this->library->findDocumentsByUuid($doc->getUuid())->current()) {
            throw new \InvalidArgumentException(
                "No document found by uuid '{$doc->getUuid()}'",
                StatusCodeInterface::STATUS_NOT_FOUND
            );
        }
        
        if ($doc->isLinkedTo($key)) {
            return new Response\EmptyResponse(StatusCodeInterface::STATUS_FORBIDDEN);
        }
        
        
        // delete document, if no links exist
        
        return new Response\EmptyResponse(501);
    }
    
    
}