<?php

namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Engine;
/**
 * Description of RefreshTokenRepository
 *
 * @author oleg
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface {

    public function getNewRefreshToken() {
        return \Engine::GetEntity("Oauth_RefreshToken");
    }

    public function isRefreshTokenRevoked($tokenId) {
        $oRefreshToken = Engine::getInstance()->Oauth_GetRefreshTokenByFilter([
            'token' => $tokenId
        ]);
        
        if(!$oRefreshToken){
            return true;
        }
        return false;
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $oRefreshToken) {
        $oRefreshToken->Save();
    }

    public function revokeRefreshToken($tokenId) {
        $oRefreshToken = Engine::getInstance()->Oauth_GetRefreshTokenByFilter([
            'token' => $tokenId
        ]);
        
        $oRefreshToken->Delete();
    }

}
