<?php


namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * Description of AuthCodeRepository
 *
 * @author oleg
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface {

    public function getNewAuthCode() {
        return new AuthCodeEntity;
    }

    public function isAuthCodeRevoked($codeId) {
        $oAuthCode = Engine::getInstance()->Oauth_GetAuthCodeByFilter([
            'id' => $codeId
        ]);
        
        if(!$oAuthCode){
            return true;
        }
        return false;
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) {
        $oDate = $authCodeEntity->getExpiryDateTime();
        
        $oAuthCode = Engine::GetEntity('Oauth_AuthCode', [
            'id'        => $authCodeEntity->getIdentifier(),
            'expiry'    => $oDate->format("Y-m-d H:i:s"),
            'live'      => $oDate->diff( new \DateTime)->format("%s"),
            'user_id'   => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
        ]);
        
        $aScopes = $authCodeEntity->getScopes();
        
        foreach ($aScopes as $eScope) {
            $oAuthCode->addScope(json_encode($eScope));
        }
        
        $oAuthCode->Save();
    }

    public function revokeAuthCode($codeId) {
        $oAuthCode = Engine::getInstance()->Oauth_GetAuthCodeByFilter([
            'id' => $codeId
        ]);
        
        $oAuthCode->Delete();
    }

}
