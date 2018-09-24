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
        $this->Logger_Debug('start client approve');
        $this->Logger_Debug('auth_request_key:'. getRequest('auth_request_key'));
        if($sAuthRequest = $this->Session_Get( getRequest('auth_request_key') )){
            $this->oAuthRequest = unserialize($sAuthRequest);
        }
        
        
        if(!$this->oAuthRequest){
            $this->Logger_Debug('No Find oAuthRequest error');
            return Router::ActionError($this->Lang_Get('oauth.notices.no_auth_request'));
        }
        $this->Logger_Debug('Find oAuthRequest:'. json_encode($this->oAuthRequest));
        if(!$eClient = $this->oAuthRequest->getClient()){
            $this->Logger_Debug('No Find $eClient error');
            return Router::ActionError($this->Lang_Get('oauth.notices.no_client'));
        }
        $this->Logger_Debug('Find $eClient:'. json_encode($eClient));
        
        $aScopes = $this->oAuthRequest->getScopes();
        $this->Logger_Debug('getScopes:'. json_encode($aScopes));
        $aScopeRequested = [];
        foreach ($aScopes as $oScope) {
            if($oScope->getRequested()){
                $aScopeRequested[] = $oScope;
            }
        }
        $this->Logger_Debug('aScopeRequested:'. json_encode($aScopeRequested));
        
        
        $oClient = $this->Oauth_GetClientByFilter([
            'id' => $eClient->getIdentifier(),
        ]);
        
        if(isPost()){
            $this->Logger_Debug('User submit');
            /*
             * Если не подтверждено
             */
            if(!getRequest('approve')){
                $this->Logger_Debug('User not approve. go:'. $this->oAuthRequest->getRedirectUri());
                Router::Location( $this->oAuthRequest->getRedirectUri() );
            }else{
                $this->Logger_Debug('User approve. setAuthorizationApproved(true)');
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
                '#where' => [
                    '1=1 or t.requested = ?d' => [0]
                ]
            ]);
            $this->Logger_Debug('setScopes:'.json_encode($aScopesApprove));
            $this->oAuthRequest->setScopes($aScopesApprove);            
            
            $this->Session_Set( getRequest('auth_request_key'), serialize($this->oAuthRequest) );
            $this->Logger_Debug('Set oAuthRequest in sess: '.getRequest('auth_request_key').' '.json_encode($this->oAuthRequest));
            $this->Logger_Debug('Go to: '.urldecode(getRequest('return_path')));
            Router::Location( urldecode(getRequest('return_path')) );
        }        
        
        $this->Viewer_Assign('sAppDefImage', $this->Component_GetWebPath('app').'/image/app100.png');
        $this->Viewer_Assign('oClient', $oClient);
        $this->Viewer_Assign('sCancelUri', Router::GetPath('oauth/authorization_cancel'));
        $this->Viewer_Assign('aScopes', $aScopeRequested);
        
    }
}
