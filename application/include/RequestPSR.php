<?php

namespace Psr\Http\Message;

/**
 * Description of RequestPSR
 *
 * @author oleg
 */
class RequestPSR7 implements ServerRequestInterface {

    private $aQueryParams = [];

    public function __construct() {
        $this->aQueryParams = $_REQUEST;
    }

    public function getAttribute($name, $default = null) {
        
    }

    public function getAttributes() {
        
    }

    public function getBody() {
        
    }

    public function getCookieParams() {
        
    }

    public function getHeader($name) {
        
    }

    public function getHeaderLine($name) {
        
    }

    public function getHeaders() {
        
    }

    public function getMethod() {
        
    }

    public function getParsedBody() {
        
    }

    public function getProtocolVersion() {
        
    }

    public function getQueryParams() {
        return $this->aQueryParams;
    }

    public function getRequestTarget() {
        
    }

    public function getServerParams() {
        
    }

    public function getUploadedFiles() {
        
    }

    public function getUri(){
        
    }

    public function hasHeader($name) {
        
    }

    public function withAddedHeader($name, $value) {
        
    }

    public function withAttribute($name, $value) {
        
    }

    public function withBody(StreamInterface $body) {
        
    }

    public function withCookieParams(array $cookies) {
        
    }

    public function withHeader($name, $value) {
        
    }

    public function withMethod($method) {
        
    }

    public function withParsedBody($data) {
        
    }

    public function withProtocolVersion($version) {
        
    }

    public function withQueryParams(array $query) {
        $this->aQueryParams = $query;
    }
    
    public function withQueryParam($name, $value) {
        $this->aQueryParams[$name] = $value;
    }

    public function withRequestTarget($requestTarget) {
        
    }

    public function withUploadedFiles(array $uploadedFiles) {
        
    }

    public function withUri(UriInterface $uri, $preserveHost = false) {
        
    }

    public function withoutAttribute($name) {
        
    }

    public function withoutHeader($name) {
        
    }

}
