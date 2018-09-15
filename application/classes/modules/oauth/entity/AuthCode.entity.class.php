<?php

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use \League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * Сущность кода
 *
 * @package modules.user
 * @since 1.0
 */
class ModuleOauth_EntityAuthCode extends EntityORM implements AuthCodeEntityInterface
{
    
   

    protected $aJsonFields = ['scopes'];
            
    protected $aRelations = array(
        'client' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleOauth_EntityClient', 'client_id'),
        'user' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id')
    );
    
    public function addScope(ScopeEntityInterface $scope) {
        if(!isset($this->_aData['scopes'])){
            $this->_aData['scopes'] = [];
        }
        $this->_aData['scopes'][] = $scope->getIdentifier();
    }

    public function getClient(){
        return parent::getClient();
    }

    public function getExpiryDateTime(){
        return DateTime::createFromFormat("Y-m-d H:i:s", parent::getExpiry());
    }

    public function getIdentifier(){
        return parent::getId();
    }

    public function getRedirectUri() {
        return parent::getRedirectUri();
    }

    public function getScopes(){
        $aScopes = [];
        foreach (parent::getScopes() as $identifier) {
            $eScope = new League\OAuth2\Server\Entities\ScopeEntity();
            $eScope->setIdentifier($identifier);
            $aScopes[] = $eScope;
        }
        return $aScopes;
    }

    public function getUserIdentifier() {
        return parent::getUserId();
    }

    public function setClient(ClientEntityInterface $client) {
        parent::setClient($client);     
        parent::setClientId($client->getIdentifier());
    }

    public function setExpiryDateTime(\DateTime $dateTime) {
        parent::setExpiry($dateTime->format("Y-m-d H:i:s"));
        $this->setLive($dateTime->getTimestamp() -  (new \DateTime('now'))->getTimestamp());
    }

    public function setIdentifier($identifier) {
        parent::setId($identifier);
    }

    public function setRedirectUri($uri) {
        parent::setRedirectUri($uri);
    }

    public function setUserIdentifier($identifier) {
        parent::setUserId($identifier);
    }

}