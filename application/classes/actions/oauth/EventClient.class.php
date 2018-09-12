<?php


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
        if($sAuthRequest = $this->Session_Get('oAuthRequest')){
            $this->oAuthRequest = unserialize($sAuthRequest);
        }
        
//        if(!$this->oAuthRequest){
//            return Router::ActionError($this->Lang_Get('oauth.notices.no_auth_request'));
//        }
//        
//        if(!$eClient = $this->oAuthRequest->getClient()){
//            return Router::ActionError($this->Lang_Get('oauth.notices.no_client'));
//        }
        
        $aScopes = $this->oAuthRequest->getScopes();
        
        $aScopeIds = json_decode($aScopes);
        
        $aScopes = $this->Oauth_GetScopeItemsByFilter([
            'id in' => $aScopeIds,
            'access in' => ['open', 'requested']
        ]);
        
        if(!count($aScopes)){
            return Router::ActionError($this->Lang_Get('oauth.notices.no_scopes'));
        }
        
        $this->Viewer_Assign('sAppDefImage', $this->Component_GetWebPath('app').'/image/app100.png');
        $this->Viewer_Assign('aScopes', $aScopes);
        
    }
}
