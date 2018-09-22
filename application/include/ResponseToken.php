<?php

use \League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * Слой для внедрения метода
 *
 * @author oleg
 */
class ResponseToken extends BearerTokenResponse{

    public function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        return ['user_id' => $accessToken->getUserIdentifier()];
    }
}
