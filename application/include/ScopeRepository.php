<?php


namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ClientEntity;
use League\OAuth2\Server\Entities\ScopeEntity;
use Engine;

/**
 * Description of Scope
 *
 * @author oleg
 */
class ScopeRepository implements ScopeRepositoryInterface {

    
    public function getScopeEntityByIdentifier($identifier) {
        
        $oScope = Engine::getInstance()->Oauth_GetScopeByIdentifier($identifier);
        if(!$identifier){
            $oScope = Engine::GetEntity('Oauth_Scope');
        }
        
        return $oScope;
    }
    
    
    
    public function finalizeScopes(array $aScopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null){
        /*
         * В этом методе подготавливаются области доступа перед передачей токена или кода
         * Можно убрать или добавить закрытые или открытые области
         */
        $aOpenScopes = Engine::getInstance()->Oauth_GetScopeItemsByFilter([
            'requested'        => 0
        ]);
        
        foreach ($aOpenScopes as $oOpenScope) {
            if(!isset($aScopes[$oOpenScope->getIdentifier()])){
                $aScopes[$oOpenScope->getIdentifier()] = $oOpenScope;
            }
        }
                 
        
        return $aScopes;
        
    } 
}
