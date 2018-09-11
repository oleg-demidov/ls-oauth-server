<?php
/**
 * Сущность приложения
 *
 * @package modules.user
 * @since 1.0
 */
class ModuleOauth_EntityClient extends EntityORM
{

    protected $aRelations = array(
        'access_tokens' => array(self::RELATION_TYPE_HAS_MANY, 'ModuleOauth_EntityAccessToken', 'client_id'),
    );

    
}