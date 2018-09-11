<?php

class ModuleOauth_EntityAccessToken extends EntityORM
{
    protected $aJsonFields = array('scopes');

    protected $aRelations = array(
        'client' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleOauth_EntityClient', 'client_id'),
        'user' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id')
    );
    
    public function addScope($sScope) {
        if(!is_array($this->_aData['scopes'])){
            $this->_aData['scopes'] = [];
        }
        $this->_aData['scopes'][] = $sScope;
    }
}