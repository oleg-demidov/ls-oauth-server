<?php

/**
 * Description of AccessTokenRepository
 *
 * @author oleg
 */
namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface {
    
        
    public function getNewToken(ClientEntityInterface $clientEntity, array $aScopes, $userIdentifier = null){
        
        $eAccessToken = new AccessTokenEntity;        
        
        return $eAccessToken;
        
    }

    public function isAccessTokenRevoked($tokenId) {
        $oAccessToken = Engine::getInstance()->Oauth_GetAccessTokenByFilter([
            'id' => $tokenId
        ]);
        
        if(!$oAccessToken){
            return true;
        }
        return false;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $eAccessToken) {
        
        $oDate = $eAccessToken->getExpiryDateTime();
        
        $oAccessToken = Engine::GetEntity('Oauth_AccessToken', [
            'id'        => $eAccessToken->getIdentifier(),
            'expiry'    => $oDate->format("Y-m-d H:i:s"),
            'live'      => $oDate->diff( new \DateTime)->format("%s"),
            'user_id'   => $eAccessToken->getUserIdentifier(),
            'client_id' => $eAccessToken->getClient()->getIdentifier(),
        ]);
        
        $aScopes = $eAccessToken->getScopes();
        
        foreach ($aScopes as $eScope) {
            $oAccessToken->addScope(json_encode($eScope));
        }
        
        $oAccessToken->Save();
        
    }

    public function revokeAccessToken($tokenId) {
        $oAccessToken = Engine::getInstance()->Oauth_GetAccessTokenByFilter([
            'id' => $tokenId
        ]);
        
        $oAccessToken->Delete();
    }

}
