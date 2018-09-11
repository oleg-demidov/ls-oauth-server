<?php

/**
 * Description of AccessTokenRepository
 *
 * @author oleg
 */
namespace League\OAuth2\Server\Entities;

class ClientRepository implements \League\OAuth2\Server\Repositories\ClientRepositoryInterface {
    
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
