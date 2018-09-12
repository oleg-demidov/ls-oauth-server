<?php

namespace League\OAuth2\Server\Entities;

/**
 * Description of UserEntity
 *
 * @author oleg
 */
class UserEntity implements UserEntityInterface {
    
    use Traits\EntityTrait;
    
    private $sEmail;

    public function setEmail($sEmail) {
        $this->sEmail = $sEmail;
    }
    
    public function getEmail() {
        return $this->sEmail;
    }

}
