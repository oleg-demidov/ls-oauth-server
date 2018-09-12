<?php

/**
 * Обрабатывает авторизацию
 *
 * @package actions
 * @since 2.0
 */
class ActionOauth extends Action
{
    
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
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        $this->RegisterEventExternal("AuthCode", "ActionOauth_EventAuthCode");
        
        $this->AddEvent('authorization_code', 'AuthCode::EventAuth');
        
    }


}