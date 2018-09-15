<?php


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
             * Определяем ключ для AuthRedirect
             */
            $iAuthRequestKey = 'oAuthRequest';
            /*
             * Дополнительные параметры для редиректов
             */
            $sQuery = http_build_query([
                'return_path' => urlencode( Router::GetPath('oauth/authorization_code')),
                'auth_request_key' => $iAuthRequestKey
            ]);
            /*
            * Проверяем нет ли уже AuthRequest в сессии
            */
            if(!$sAuthRequest = $this->Session_Get($iAuthRequestKey)){
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
                
                $this->Session_Set($iAuthRequestKey, serialize($this->oAuthRequest));
            }else{
                $this->Session_Set($iAuthRequestKey, serialize($this->oAuthRequest));
                /*
                 * Отправляем на авторизацию
                 */
                Router::Location(Router::GetPath('auth'). '?' . $sQuery);
            }
            /*
             * Попытка отправить имеющийся подходящий код
             */
            $this->TryResponseAuthCode();
            /*
             * Отправляем на проверку приложения и прав
             */
            if(!$this->oAuthRequest->isAuthorizationApproved()){
                Router::Location(Router::GetPath('oauth/client_approve'). '?' . $sQuery);
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
            Router::Location($oResponse->getHeaders()['Location'][0]);
            
            $this->SetTemplate(false);

        }  catch (\Exception $exception) {
            return Router::ActionError($exception->getMessage());
            
        }
        
    }
    
    public function TryResponseAuthCode() {
        $aFilter = [
            'user_id' => $this->oAuthRequest->getUser()->getIdentifier(),
            'client_id'=> $this->oAuthRequest->getClient()->getIdentifier()
        ];
        
        $aScopes = $this->oAuthRequest->getScopes();
        $aScopeStr = [0];
        foreach ($aScopes as $eScope) {
            $aScopeStr[] = $eScope->getIdentifier();
        }
        /*
         * Выбираем скоупы с необходимостью подтверждения из тех что запрошены
         */
        $aScopes = $this->Oauth_GetScopeItemsByFilter([
            'requested' => 1,
            'id in' => $aScopeStr,
            '#index-from' => 'id'
        ]);
        if(count($aScopes)){
            $aFilter['scopes'] = json_encode(array_keys($aScopes));
        }
        /*
         * Ищем код для приложения и пользователя с подтвержденными скоупами выше
         */
        $oAuthCode = $this->Oauth_GetAuthCodeByFilter($aFilter);
        
        $oClient = $this->Oauth_GetClientById($this->oAuthRequest->getClient()->getIdentifier());
        
        if($oAuthCode and $oClient){
            /*
             * Сбрасываем сессию и отправляем код
             */
            $this->Session_Drop('oAuthRequest');
            $this->Session_Drop('state');
            Router::Location($oClient->getRedirectUri().'?code='.$oAuthCode->getId());
        }
    }
}
