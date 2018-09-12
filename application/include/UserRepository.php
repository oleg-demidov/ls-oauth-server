<?php

namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntity;

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
        
        $eUser = new UserEntity;
        $eUser->setIdentifier($oUser->getId());
        
        return $eUser;
        
    }

}
