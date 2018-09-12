<?php

namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
/**
 * Description of RefreshTokenRepository
 *
 * @author oleg
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface {

    public function getNewRefreshToken() {
        return new RefreshTokenEntity;
    }

    public function isRefreshTokenRevoked($tokenId) {
        $oRefreshToken = Engine::getInstance()->Oauth_GetRefreshTokenByFilter([
            'id' => $tokenId
        ]);
        
        if(!$oRefreshToken){
            return true;
        }
        return false;
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) {
        $oDate = $refreshTokenEntity->getExpiryDateTime();
        
        $oRefreshToken = Engine::GetEntity('Oauth_RefreshToken', [
            'id'        => $refreshTokenEntity->getIdentifier(),
            'expiry'    => $oDate->format("Y-m-d H:i:s"),
            'live'      => $oDate->diff( new \DateTime)->format("%s"),
            'access_token_id' => $refreshTokenEntity->getAccessToken()->getIdentifier(),
        ]);
        
        $oRefreshToken->Save();
    }

    public function revokeRefreshToken($tokenId) {
        $oRefreshToken = Engine::getInstance()->Oauth_GetRefreshTokenByFilter([
            'id' => $tokenId
        ]);
        
        $oRefreshToken->Delete();
    }

}
