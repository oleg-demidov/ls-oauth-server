<?php

/**
 * Description of AccessTokenRepository
 *
 * @author oleg
 */
use \League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use \League\OAuth2\Server\Entities\ClientEntityInterface;
use \League\OAuth2\Server\Entities\ScopeEntityInterface;
use \League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface {

    public function getNewToken(ClientEntityInterface $clientEntity, ScopeEntityInterface $scopes, $userIdentifier = null): AccessTokenEntityInterface {
        
        
        
    }

    public function isAccessTokenRevoked($tokenId): bool {
        
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
        
    }

    public function revokeAccessToken($tokenId) {
        
    }

}
