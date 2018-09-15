<?php

use \League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use \League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class ModuleOauth_EntityRefreshToken extends EntityORM implements RefreshTokenEntityInterface
{

    protected $aRelations = array(
        'access_token' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleOauth_EntityAccessToken', 'access_token_id'),
    );

    public function getAccessToken() {
        return parent::getAccessToken();
    }

    public function getExpiryDateTime(){
        return DateTime::createFromFormat("Y-m-d H:i:s", parent::getExpiry());
    }

    public function getIdentifier(){
        return parent::getId();
    }

    public function setAccessToken(AccessTokenEntityInterface $accessToken) {
        parent::setAccessToken($accessToken);
        parent::setAssessTokenId($accessToken->getIdentifier());
    }

    public function setExpiryDateTime(\DateTime $dateTime) {
        parent::setExpiry($dateTime->format("Y-m-d H:i:s"));
        parent::setLive($dateTime->getTimestamp() - (new DateTime)->getTimestamp());
    }

    public function setIdentifier($identifier) {
        parent::setId($identifier);
    }

}