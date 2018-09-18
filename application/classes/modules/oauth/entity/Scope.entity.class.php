<?php

use League\OAuth2\Server\Entities\ScopeEntityInterface;
/**
 * Сущность области
 *
 * @package modules.user
 * @since 1.0
 */
class ModuleOauth_EntityScope extends EntityORM implements ScopeEntityInterface
{

   
    public function getIdentifier(){
        return parent::getIdentifier();
    }

    public function jsonSerialize() {
        return $this->getIdentifier();
    }

}