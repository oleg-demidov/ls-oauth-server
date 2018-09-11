<?php

namespace League\OAuth2\Server\Entities;

/**
 * Description of Scope
 *
 * @author oleg
 */
class ScopeEntity implements ScopeEntityInterface {
    
    use Traits\EntityTrait;

    public function jsonSerialize() {
        return $this->getIdentifier();
    }

}
