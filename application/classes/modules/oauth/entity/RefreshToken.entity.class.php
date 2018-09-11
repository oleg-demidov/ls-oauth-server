<?php

class ModuleOauth_EntityRefreshToken extends EntityORM
{
    
    protected $aRelations = array(
        'access_token' => array(self::RELATION_TYPE_BELONGS_TO, 'ModuleOauth_EntityAccessToken', 'access_token_id'),
    );


}