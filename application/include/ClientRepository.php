<?php

/**
 * Description of AccessTokenRepository
 *
 * @author oleg
 */
namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntity;
use Engine;

class ClientRepository implements ClientRepositoryInterface {
    
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true) {
        $eClient = new ClientEntity;
        
        /*
         * Проверяем на существование клиента (приложение)
         */
        $oClient = Engine::getInstance()->Oauth_GetClientByFilter([
            'id' => $clientIdentifier
        ]);

        if(!$oClient){
            return false;
        }
        
        /*
         * Проверяем секрет клиента если нужно
         */
        if($mustValidateSecret){
            
            if($oClient->getSecret() != $clientSecret){
                return false;
            }
        }
        
        return $eClient;
    }

}
