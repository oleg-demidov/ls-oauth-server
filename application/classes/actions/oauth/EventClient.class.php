<?php

use League\OAuth2\Server\Entities\ScopeEntity;
/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionOauth_EventClient extends Event {
    
    public function EventApprove() {
        /*
        * Проверяем нет ли уже AuthRequest в сессии
        */
        if($sAuthRequest = $this->Session_Get( getRequest('auth_request_key') )){
            $this->oAuthRequest = unserialize($sAuthRequest);
        }
        
        if(!$this->oAuthRequest){
            return Router::ActionError($this->Lang_Get('oauth.notices.no_auth_request'));
        }
        
        if(!$eClient = $this->oAuthRequest->getClient()){
            return Router::ActionError($this->Lang_Get('oauth.notices.no_client'));
        }
        
        $aScopes = $this->oAuthRequest->getScopes();
        $aScopeRequested = [];
        foreach ($aScopes as $oScope) {
            if($oScope->getRequested()){
                $aScopeRequested[] = $oScope;
            }
        }
        
        
        
        $oClient = $this->Oauth_GetClientByFilter([
            'id' => $eClient->getIdentifier(),
        ]);
        
        if(isPost()){
            /*
             * Подтверждение приложения
             */
            if(getRequest('approve')){
                $this->oAuthRequest->setAuthorizationApproved(true);
            }
            /*
             * Подтвержденные скоупы
             */
            $aScopeIds = [0];
            foreach (getRequest('scopes', []) as $iScopeId => $bApprove) {
                if(!$bApprove){
                    continue;
                }
                $aScopeIds[] = $iScopeId;
            }
            $aScopesApprove = $this->Oauth_GetScopeItemsByFilter([
                'id in' => $aScopeIds,
            ]);
            $this->oAuthRequest->setScopes($aScopesApprove);
            
            $this->Session_Set( getRequest('auth_request_key'), serialize($this->oAuthRequest) );
            Router::Location( urldecode(getRequest('return_path')) );
        }        
        
        $this->Viewer_Assign('sAppDefImage', $this->Component_GetWebPath('app').'/image/app100.png');
        $this->Viewer_Assign('oClient', $oClient);
        $this->Viewer_Assign('aScopes', $aScopeRequested);
        
    }
}
