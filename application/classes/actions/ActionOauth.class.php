<?php

use \Slim\Http\Request;
/**
 * Обрабатывает авторизацию
 *
 * @package actions
 * @since 2.0
 */
class ActionOauth extends Action
{
    public $oServer;
    
    public $oRequest;
    
    public $oAuthRequest;
    
    public $sAuthRequestKey = 'oAuthRequest';
    /**
     * Инициализация
     */
    public function Init()
    {
        /**
         * Устанавливаем дефолтный евент
         */
        $this->SetDefaultEvent('authorization_code');
        /**
         * Отключаем отображение статистики выполнения
         */
        Router::SetIsShowStats(false);
        /*
         * Объявление обязательного интерфейса для oauth сервера
         */        
        
        $this->oRequest = Request::createFromGlobals($_SERVER);
        
        /*
         * Удалить все устаревшие коды
         */
        $this->Oauth_DeleteAuthCodeItemsByFilter([
            '#where' => [
                't.expiry < ?' => [(new \DateTime('now'))->format("Y-m-d H:i:s")]
            ]
            
        ]);
        
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        $this->RegisterEventExternal("AuthCode", "ActionOauth_EventAuthCode");        
        $this->AddEvent('authorization_code', 'AuthCode::EventAuth');
        
        $this->AddEvent('authorization_cancel', 'EventCancel');
        
        $this->RegisterEventExternal("Client", "ActionOauth_EventClient");
        $this->AddEvent('client_approve', 'Client::EventApprove');
        
        $this->RegisterEventExternal("Token", "ActionOauth_EventToken");
        $this->AddEvent('access_token',  'Token::EventGet');
        
    }
    
    public function EventCancel() {
        if($sAuthRequest = $this->Session_Get($this->sAuthRequestKey)){
            $this->oAuthRequest = unserialize($sAuthRequest);
            $this->Session_Drop('oAuthRequest');
            $this->Session_Drop('state');
            Router::Location($this->oAuthRequest->getRedirectUri());
        }
        return Router::ActioError("No client find");
    }


}