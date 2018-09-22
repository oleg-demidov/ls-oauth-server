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
        $this->Logger_Debug('Start AuthCode');
        $this->Logger_Debug('Request params give:'.json_encode($this->oRequest->getQueryParams()));
       
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
                $this->Logger_Debug('NO AuthRequest in session.Try validate oRequest params:'.json_encode($this->oRequest->getQueryParams()));
                $this->oAuthRequest = $this->oServer->validateAuthorizationRequest($this->oRequest);
            }else{
                $this->Logger_Debug('Find AuthRequest in session:'.json_encode($sAuthRequest));
                $this->oAuthRequest = unserialize($sAuthRequest);
                /*
                 * Если state передан и  иной чем в сессии обновляем AuthRequest
                 */
                
                if(getRequest('state', $this->Session_Get('state')) != $this->oAuthRequest->getState()){
                    $this->Logger_Debug('Find new state in session.Update validate AuthRequest:'.json_encode($sAuthRequest));
                    $this->Session_Drop('oAuthRequest');
                    $this->Session_Drop('state');
                    $this->oAuthRequest = $this->oServer->validateAuthorizationRequest($this->oRequest);
                }
            }
            /*
             * Устанавливаем redirect_uri чтобы не был обязательным в запросе
             */
            $this->oAuthRequest->setRedirectUri(getRequest('redirect_uri', $this->oAuthRequest->getClient()->getRedirectUri()));
            $this->Logger_Debug('Request update redirect_uri:'.$this->oAuthRequest->getRedirectUri());
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
                $this->Logger_Debug('State inset:'.$this->oAuthRequest->getState());
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
                $this->Logger_Debug('Set User:'. print_r($this->oAuthRequest->getUser(),true));
                $this->Session_Set($this->sAuthRequestKey, serialize($this->oAuthRequest));
                $this->Logger_Debug('Set oAuthRequest in session:'. $this->sAuthRequestKey);
            }else{
                $this->Session_Set($this->sAuthRequestKey, serialize($this->oAuthRequest));
                /*
                 * Отправляем на авторизацию
                 */
                $this->Logger_Debug('Redirect User to login:'. Router::GetPath('auth'). '?' . $sQuery);
                Router::Location(Router::GetPath('auth'). '?' . $sQuery);
            }
            
            
            if(!$this->oAuthRequest->isAuthorizationApproved()){
                $this->Logger_Debug('isAuthorizationApproved:false');
                /*
                 * Проверка подтверждал ли пользователь запрашиваемые скоупы для этого приложения
                 * если да то setAuthorizationApproved(true)
                 */
                if(!$this->AuthCodeExists()){
                    /*
                    * Отправляем на проверку приложения и прав
                    */
                    $this->Logger_Debug('Redirect client approve:'.Router::GetPath('oauth/client_approve'). '?' . $sQuery);
                    Router::Location(Router::GetPath('oauth/client_approve'). '?' . $sQuery);
                }
            }          
            $this->Logger_Debug('isAuthorizationApproved:true');
            /*
             * Отправка кода
             */
            $this->Session_Drop('oAuthRequest');
            $this->Session_Drop('state');
                  
            $this->Logger_Debug('completeAuthorizationRequest'. print_r($this->oAuthRequest,true));
            $oResponse = new \Slim\Http\Response();
            $oResponse = $this->oServer->completeAuthorizationRequest($this->oAuthRequest, $oResponse);
            /*
             * Перенаправление с кодом
             */
            $aLocation = $oResponse->getHeader('Location');
            if(!is_array($aLocation) and !count($aLocation)){
                throw OAuthServerException::serverError("Unknown error");
            }
            $this->Logger_Debug('Give code '. print_r($aLocation,true));
            
            Router::Location(array_shift($aLocation));
            
            $this->SetTemplate(false);

        }  catch (\Exception $exception) {
            return Router::ActionError($exception->getHint(),$exception->getMessage());
            
        }
        
    }
    
    
    public function AuthCodeExists() {
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
        
        $aScopes = $this->oAuthRequest->getScopes();
        /*
         * Выбираем скоупы с необходимостью подтверждения из тех что запрошены
         */
        $this->Logger_Debug('Try find AuthCode by scopes:'. print_r($aScopes,true));
        $aScopesRequested = [0];
        foreach ($aScopes as $oScope) {
            if($oScope->getRequested()){
                $aScopesRequested[] = $oScope->getId();
            }
        }
        $aScopesApprove = $this->Oauth_GetScopeItemsByFilter([
            'id in' => $aScopesRequested,
            '#where' => [
                '1=1 or t.requested = ?d' => [0]
            ],
            '#index-from' => 'identifier'
        ]);               
        
        if(count(array_keys($aScopesApprove))){
            $aFilter['scopes'] = json_encode(array_keys($aScopesApprove));
        }
        $this->Logger_Debug('Try find AuthCode by filter:'.json_encode($aFilter));
        /*
         * Ищем код и токен для приложения и пользователя с подтвержденными скоупами выше
         */
        $oAuthCode = $this->Oauth_GetAuthCodeByFilter($aFilter);
        $oAccessToken = $this->Oauth_GetAccessTokenByFilter($aFilter);
        
        $oClient = $this->oAuthRequest->getClient();
        
        if(($oAuthCode or $oAccessToken) and $oClient){
            /*
             * Удаляем старый код, так как все равно создастся новый
             */
            $this->Logger_Debug('Find:'.json_encode($oAuthCode));
            $oAuthCode->Delete();
            $this->oAuthRequest->setScopes($aScopesApprove); 
            $this->oAuthRequest->setAuthorizationApproved(true);
            return true;
        }
        $this->Logger_Debug('No Find');
        return false;
    }
    
    
}
