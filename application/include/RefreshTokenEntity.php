<?php


namespace League\OAuth2\Server\Entities;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * Description of RefreshTokenEntity
 *
 * @author oleg
 */
class RefreshTokenEntity implements RefreshTokenEntityInterface {
    
    use Traits\EntityTrait, Traits\RefreshTokenTrait;

}
