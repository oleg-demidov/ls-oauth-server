<?php

use \Slim\Http\Request;
/**
 * Обрабатывает авторизацию
 *
 * @package actions
 * @since 2.0
 */
class ActionApi extends Action
{
    public $oServer;
    
    public $oRequest;
    
    /**
     * Инициализация
     */
    public function Init()
    {
        /**
         * Устанавливаем дефолтный евент
         */
        $this->SetDefaultEvent('me');
        /**
         * Отключаем отображение статистики выполнения
         */
        Router::SetIsShowStats(false);
        /*
         * Объявление обязательного интерфейса для oauth сервера
         */                
        $this->oRequest = Request::createFromGlobals($_SERVER);
        
        $this->oServer = $this->Oauth_GetResourceServer();
        
        try {
            
            $this->oRequest = $this->oServer->validateAuthenticatedRequest($this->oRequest);

        }  catch (\Exception $exception) {
            
            $this->Message_AddError($exception->getMessage(),$exception->getHint());
            $this->Viewer_AssignAjax('iErrorCode', $exception->getCode() );            
            $this->Viewer_DisplayAjax();
        }
        
        
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        
        $this->RegisterEventExternal("ApiProfile", "ActionApi_EventProfile");        
        $this->AddEvent('me', 'ApiProfile::EventMe');
        
    }
    

}