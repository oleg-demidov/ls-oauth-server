<?php

/**
 * Description of AccessTokenEntity
 *
 * @author oleg
 */

use \League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

abstract class AccessTokenEntity implements AccessTokenEntityInterface{

    public function addScope(ScopeEntityInterface $scope) {
        
    }

    public function convertToJWT(\League\OAuth2\Server\CryptKey $privateKey): \Lcobucci\JWT\Token {
        
    }

    public function getClient(): \League\OAuth2\Server\Entities\ClientEntityInterface {
        
    }

    public function getExpiryDateTime(): \DateTime {
        
    }

    public function getIdentifier(): string {
        
    }

    public function getScopes(){
    
    }

    public function getUserIdentifier() {

    }

    public function setClient(\League\OAuth2\Server\Entities\ClientEntityInterface $client) {

    }

    public function setExpiryDateTime(\DateTime $dateTime) {

    }

    public function setIdentifier($identifier) {

    }

    public function setUserIdentifier($identifier) {

    }

}
