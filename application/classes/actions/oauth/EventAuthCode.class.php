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
        $this->oRequest->withQueryParam('response_type', 'code');
        
    }        
    
    public function EventAuth() {
        //$this->Session_Drop('oAuthRequest');

        try {
            /*
             * Определяем ключ для AuthRedirect
             */
            $iAuthRequestKey = 'oAuthRequest'.rand(0, 1000000);
            $iAuthRequestKey = getRequest('state', $iAuthRequestKey);
            /*
             * Дополнительные параметры для редиректов
             */
            $sQuery = http_build_query([
                'return_path' => Router::GetPath('oauth/authorization_code'),
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
            }
            
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
             * Отправляем на проверку приложения и прав
             */
            if(!$this->oAuthRequest->isAuthorizationApproved()){
                Router::Location(Router::GetPath('oauth/client_approve'). '?' . $sQuery);
            }          

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            //$authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            //return $server->completeAuthorizationRequest($authRequest, $response);

        }  catch (\Exception $exception) {
            return Router::ActionError($exception->getMessage());
            
        }
        
    }
}
