<?php

/**
 * Description of AccessTokenEntity
 *
 * @author oleg
 */
namespace League\OAuth2\Server\Entities;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;


class AccessTokenEntity implements AccessTokenEntityInterface{
    
    use Traits\AccessTokenTrait, Traits\EntityTrait, Traits\TokenEntityTrait;   

}
