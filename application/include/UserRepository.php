<?php

namespace League\OAuth2\Server\Entities;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;

/**
 * Description of UserRepository
 *
 * @author oleg
 */
class UserRepository implements UserRepositoryInterface{

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity) {
        $oLS = Engine::getInstance();
        
        $oUser = $oLS->User_GetUserByFilter([
            'mail'      => $username,
            'password'  => $oLS->User_MakeHashPassword($password)
        ]);
        
        if(!$oUser){
            return false;
        }
        
        return new UserEntity;
        
    }

}
