<?php

namespace League\OAuth2\Server\Entities;

/**
 * Description of AuthCodeEntity
 *
 * @author oleg
 */
class AuthCodeEntity implements AuthCodeEntityInterface {
    
    use Traits\EntityTrait, Traits\TokenEntityTrait, Traits\AuthCodeTrait; 

}
