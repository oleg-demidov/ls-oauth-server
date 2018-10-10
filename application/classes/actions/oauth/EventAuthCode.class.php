<?php

use \League\OAuth2\Server\Exception\OAuthServerException;
/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionOauth_EventAuthCode extends Event {
    
    public function Init() {
        /*
         * Инициализируем сервер и тип гранта
         */
        $this->oServer = $this->Oauth_GetServer('authorization_code');      
        /*
         * Добавляем параметр тип гранта в запрос
         */
        $this->oRequest = $this->oRequest->withQueryParams(
            array_merge(
                $this->oRequest->getQueryParams(),
                [
                    'response_type' => 'code'
                ]
            )
        );
       
    }        
    
    public function EventAuth() {

        try {
            
            /*
             * Дополнительные параметры для редиректов
             */
            $sQuery = http_build_query([
                'return_path' => urlencode( Router::GetPath('oauth/authorization_code')),
                'auth_request_key' => $this->sAuthRequestKey
            ]);
            /*
            * Проверяем нет ли уже AuthRequest в сессии
            */            
            if(!$sAuthRequest = $this->Session_Get($this->sAuthRequestKey)){
                /*
                 * Если нет запускаем новую авторизацию
                 */
                $this->oAuthRequest = $this->oServer->validateAuthorizationRequest($this->oRequest);
            }else{
                $this->oAuthRequest = unserialize($sAuthRequest);
                /*
                 * Если state передан и  иной чем в сессии обновляем AuthRequest
                 */
                
                if(getRequest('state', $this->Session_Get('state')) != $this->oAuthRequest->getState()){
                    $this->Session_Drop('oAuthRequest');
                    $this->Session_Drop('state');
                    $this->oAuthRequest = $this->oServer->validateAuthorizationRequest($this->oRequest);
                }
            }
            /*
             * Устанавливаем redirect_uri если пуст
             */
            if(!getRequest('redirect_uri')){
                $this->oAuthRequest->setRedirectUri($this->oAuthRequest->getClient()->getRedirectUri());
            }
            /*
             * Устанавливаем state если пуст
             */ 
            if(!$this->oAuthRequest->getState()){
               
                $aScopes = [];
                foreach($this->oAuthRequest->getScopes() as $eScope){
                    $aScopes[] = $eScope->getIdentifier();
                }
                
                $sState = $this->Oauth_GenerateState(
                    $this->oAuthRequest->getClient()->getIdentifier(),
                    join(',', $aScopes)
                );
                
                $this->oAuthRequest->setState( $sState );
            }
            
            
            $this->Session_Set('state', $this->oAuthRequest->getState());
            
            /*
             * Проверяем на авторизацию
             */
            if($this->User_IsAuthorization()){
                /*
                 * Конвертируем пользователя
                 */
                $eUser = $this->Oauth_GetUserEntity( $this->User_GetUserCurrent() );
                $this->oAuthRequest->setUser($eUser);
                $this->Session_Set($this->sAuthRequestKey, serialize($this->oAuthRequest));
            }else{
                $this->Session_Set($this->sAuthRequestKey, serialize($this->oAuthRequest));
                /*
                 * Отправляем на авторизацию
                 */
                Router::Location(Router::GetPath('auth'). '?' . $sQuery);
            }
            
            
            if(!$this->oAuthRequest->isAuthorizationApproved()){
                /*
                 * Проверка подтверждал ли пользователь запрашиваемые скоупы для этого приложения
                 * если да то setAuthorizationApproved(true)
                 */
                if(!$this->AuthCodeExists()){
                    /*
                    * Отправляем на проверку приложения и прав
                    */
                    Router::Location(Router::GetPath('oauth/client_approve'). '?' . $sQuery);
                }
            }          
            /*
             * Отправка кода
             */
            $this->Session_Drop('oAuthRequest');
            $this->Session_Drop('state');
                  
            $oResponse = new \Slim\Http\Response();
            $oResponse = $this->oServer->completeAuthorizationRequest($this->oAuthRequest, $oResponse);
            /*
             * Перенаправление с кодом
             */
            $aLocation = $oResponse->getHeader('Location');
            if(!is_array($aLocation) and !count($aLocation)){
                throw OAuthServerException::serverError("Unknown error");
            }
            
            Router::Location(array_shift($aLocation));
            
            $this->SetTemplate(false);

        }  catch (\Exception $exception) {
            return Router::ActionError($exception->getHint(),$exception->getMessage());
            
        }
        
    }
    
    
    public function AuthCodeExists() {
        
        $aScopes = $this->oAuthRequest->getScopes();
        /*
         * Выбираем скоупы с необходимостью подтверждения из тех что запрошены
         */
        $aScopesRequested = [0];
        foreach ($aScopes as $oScope) {
            if($oScope->getRequested()){
                $aScopesRequested[] = $oScope->getId();
            }
        }
        $FilterScope = [
            'id in' => $aScopesRequested,
            '#where' => [
                '1=1 or t.requested = ?d' => [0]
            ],
            '#index-from' => 'identifier'
        ];
        
        $aScopesApprove = $this->Oauth_GetScopeItemsByFilter($FilterScope);    
        /*
         * Если клиент с особыми правами пропускаем approve
         */
        if($this->oAuthRequest->getClient()->getExtraApprove()){
            $this->oAuthRequest->setScopes($aScopesApprove); 
            $this->oAuthRequest->setAuthorizationApproved(true);
            return true;
        }
        /*
        * Отменяем восстановление кода
        */
        if(!Config::Get('module.oauth.fast_auth_code')){
           return false;
        }
        
        $aFilter = [
            'user_id' => $this->oAuthRequest->getUser()->getIdentifier(),
            'client_id'=> $this->oAuthRequest->getClient()->getIdentifier(),
            '#where' => ['t.expiry > ?'    => [(new DateTime)->format("Y-m-d H:i:s")]]
        ];
        
        if(count(array_keys($aScopesApprove))){
            $aFilter['scopes'] = json_encode(array_keys($aScopesApprove));
        }
        /*
         * Ищем код и токен для приложения и пользователя с подтвержденными скоупами выше
         */
        $oAuthCode = $this->Oauth_GetAuthCodeByFilter($aFilter);
        $oAccessToken = $this->Oauth_GetAccessTokenByFilter($aFilter);
        
        
        if($oAuthCode){
            /*
             * Удаляем старый код, так как все равно создастся новый
             */
            $oAuthCode->Delete();
            $this->oAuthRequest->setScopes($aScopesApprove); 
            $this->oAuthRequest->setAuthorizationApproved(true);
            return true;
        }
        if($oAccessToken){
            /*
             * Найден токен с запрашиваемыми скопами
             */
            $this->oAuthRequest->setScopes($aScopesApprove); 
            $this->oAuthRequest->setAuthorizationApproved(true);
            return true;
        }
        return false;
    }
    
    
}
