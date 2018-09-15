<?php


/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionOauth_EventRefreshToken extends Event {
    
    public function Init() {
        
        /*
         * Инициализируем сервер и тип гранта
         */
        $this->oServer = $this->Oauth_GetServer(getRequest('grant_type', 'refresh_token'));    
        
        /*
         * Добавляем параметр тип гранта по умолчанию в запрос если нет
         */
        if(!getRequest('grant_type')){
            $this->oRequest = $this->oRequest->withParsedBody(
                array_merge(
                    $this->oRequest->getParsedBody(),
                    [
                        'grant_type' => 'refresh_token'
                    ]
                )
            );
        }       
       
    }        
    
    public function EventGet() {

        try {
            $oResponse = $this->oServer->respondToAccessTokenRequest($this->oRequest, new \Slim\Http\Response());
            
            print_r((string)$oResponse->getBody());
            
            $this->SetTemplate(false);

        }  catch (\Exception $exception) {
            
            $this->Message_AddError($exception->getMessage(),$exception->getHint());
            $this->Viewer_AssignAjax('iErrorCode', $exception->getCode() );            
            $this->Viewer_DisplayAjax();
        }
        
    }
}
