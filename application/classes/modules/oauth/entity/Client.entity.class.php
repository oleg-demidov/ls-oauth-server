<?php

use League\OAuth2\Server\Entities\ClientEntityInterface;
/**
 * Сущность приложения
 *
 * @package modules.user
 * @since 1.0
 */
class ModuleOauth_EntityClient extends EntityORM implements ClientEntityInterface
{

    protected $aRelations = array(
        'access_tokens' => array(self::RELATION_TYPE_HAS_MANY, 'ModuleOauth_EntityAccessToken', 'client_id'),
    );

    
    public function getIdentifier() {
        return (string)parent::getId();
    }

    public function getName() {
        return parent::getName();
    }

    public function getRedirectUri() {
        return parent::getRedirectUri();
    }

}