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
        
        $oScope = Engine::getInstance()->Oauth_GetScopeById($identifier);
        
        if(!$oScope){
            return false;
        }
        
        $eScope = new ScopeEntity();
        $eScope->setIdentifier($identifier);
        
        return $eScope;
    }
    
    
    
    public function finalizeScopes(array $aEntScopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null){
        /*
         * В этом методе подготавливаются области доступа перед передачей токена или кода
         * Можно убрать или добавить закрытые или открытые области
         */
        $aOpenScopes = Engine::getInstance()->Oauth_GetScopeItemsByFilter([
            'access'        => 'open'
        ]);        
        
        foreach ($aOpenScopes as $oOpenScope) {
            if(!isset($aEntScopes[$oOpenScope->getId()])){
                $eScope = new ScopeEntity();
                $eScope->setIdentifier($oOpenScope->getId());
                $aEntScopes[$oOpenScope->getId()] = $eScope;
            }
        }
        
        /*
         * Убираем запрещенные скоупы
         */
        $aClosedScopes = Engine::getInstance()->Oauth_GetScopeItemsByFilter([
            'access'        => 'closed'
        ]); 
        
        foreach ($aClosedScopes as $oClosedScope) {
            if(isset($aEntScopes[$oOpenScope->getId()])){
                unset($aEntScopes[$oOpenScope->getId()]);
            }
        }  
        
        return $aEntScopes;
        
    } 
}
