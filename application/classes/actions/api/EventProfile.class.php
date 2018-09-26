<?php

use League\OAuth2\Server\Entities\ScopeEntity;
/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionApi_EventProfile extends Event {
    
    public $aScopes = [];
    
    public function Init() {
        $this->SetTemplate(false);
        /*
         * Берем скоупы
         */
        $this->aScopes = $this->oRequest->getAttribute('oauth_scopes');
    }
    
    public function EventMe() {
        
        $iUserId = $this->oRequest->getAttribute('oauth_user_id');
        
        if(!$oUser = $this->User_GetUserByFilter(['id' => $iUserId])){
            $this->Message_AddError('No find user');
            $this->Viewer_DisplayAjax();
            return;
        } 
        
        if(!in_array('profile', $this->aScopes)){
            $this->Message_AddError('No access to the scope profile');
            $this->Viewer_DisplayAjax();
            return;
        }
        
        $aProfileData = [
            'id',
            'date_create',
            'activate'
        ];
        
        if(in_array('mail', $this->aScopes)){
            $aProfileData[] = 'mail';
        }
        
        $this->Viewer_AssignAjax('profile', $oUser->_getData($aProfileData));
        $this->Viewer_DisplayAjax();
        
    }
}
