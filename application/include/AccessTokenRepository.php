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
use Engine;

class AccessTokenRepository implements AccessTokenRepositoryInterface {
    
        
    public function getNewToken(ClientEntityInterface $clientEntity, array $aScopes, $userIdentifier = null){
        
        return \Engine::GetEntity("Oauth_AccessToken");
        
    }

    public function isAccessTokenRevoked($tokenId) {
        $oAccessToken = Engine::getInstance()->Oauth_GetAccessTokenByFilter([
            'token' => $tokenId
        ]);
        
        if(!$oAccessToken){
            return true;
        }
        return false;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $oAccessToken) {
        
        $oAccessToken->Save();
        
    }

    public function revokeAccessToken($tokenId) {
        $oAccessToken = Engine::getInstance()->Oauth_GetAccessTokenByFilter([
            'token' => $tokenId
        ]);
        
        $oAccessToken->Delete();
    }

}
