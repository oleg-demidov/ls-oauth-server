<?php

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
        $this->oRequest = new Psr\Http\Message\RequestPSR7;
        
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        $this->RegisterEventExternal("AuthCode", "ActionOauth_EventAuthCode");        
        $this->AddEvent('authorization_code', 'AuthCode::EventAuth');
        
        $this->RegisterEventExternal("Client", "ActionOauth_EventClient");
        $this->AddEvent('client_approve', 'Client::EventApprove');

        
    }


}