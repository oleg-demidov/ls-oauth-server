<?php


namespace League\OAuth2\Server\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntity;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Engine;

/**
 * Description of AuthCodeRepository
 *
 * @author oleg
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface {

    public function getNewAuthCode() {
        return Engine::GetEntity('Oauth_AuthCode');
    }

    public function isAuthCodeRevoked($codeId) {
        $oAuthCode = Engine::getInstance()->Oauth_GetAuthCodeByFilter([
            'code' => $codeId
        ]);
        
        if(!$oAuthCode){
            return true;
        }
        return false;
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) {
        $authCodeEntity->Save();
    }

    public function revokeAuthCode($codeId) {
        $oAuthCode = Engine::getInstance()->Oauth_GetAuthCodeByFilter([
            'code' => $codeId
        ]);
        
        $oAuthCode->Delete();
    }

}
