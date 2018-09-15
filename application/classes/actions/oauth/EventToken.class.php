<?php


/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionOauth_EventToken extends Event {
    
    public function Init() {
        /*
         * Инициализируем сервер и тип гранта
         */
        $this->oServer = $this->Oauth_GetServer(getRequest('grant_type', 'authorization_code'));      
        /*
         * Добавляем параметр тип гранта по умолчанию в запрос если нет
         */
        if(!getRequest('grant_type')){
            $this->oRequest = $this->oRequest->withQueryParams(
                array_merge(
                    $this->oRequest->getQueryParams(),
                    [
                        'grant_type' => 'authorization_code'
                    ]
                )
            );
        }       
       
    }        
    
    public function EventGet() {

        try {
            $oResponse = $this->oServer->respondToAccessTokenRequest($this->oRequest, new \Slim\Http\Response());
            
            print_r($oResponse);
            
            $this->SetTemplate(false);

        }  catch (\Exception $exception) {
            return Router::ActionError($exception->getMessage());
            
        }
        
    }
}
