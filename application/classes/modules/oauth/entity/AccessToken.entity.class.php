<?php

use \League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use \League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class ModuleOauth_EntityAccessToken extends EntityORM implements AccessTokenEntityInterface
{
    use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
    
    protected $aJsonFields = array('scopes');

    protected $aRelations = array(
        //'client' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleOauth_EntityClient', 'client_id'),
        'user' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id')
    );
    
    public function addScope(ScopeEntityInterface $oScope) {
        if(!isset($this->_aData['scopes'])){
            $this->_aData['scopes'] = [];
        }
        if(!in_array($oScope->getIdentifier(), $this->_aData['scopes'])){
            $this->_aData['scopes'][] = $oScope->getIdentifier();
        }
    }

    public function getClient() {
        return parent::getClient();
    }

    public function getExpiryDateTime(){
        return DateTime::createFromFormat("Y-m-d H:i:s", parent::getExpiry());
    }

    public function getIdentifier() {
        return parent::getToken();
    }

    public function getScopes() {
        if(is_array(parent::getScopes())){
            return $this->Oauth_GetScopeItemsByFilter([
                'identifier in' => array_merge(parent::getScopes(),['0'])
            ]); 
        }
        return [];
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
        parent::setLive($dateTime->getTimestamp() - (new DateTime)->getTimestamp());
    }

    public function setIdentifier($identifier) {
        parent::setToken($identifier);
    }

    public function setUserIdentifier($identifier) {
        parent::setUserId($identifier);
    }


}