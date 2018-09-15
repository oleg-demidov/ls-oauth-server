<?php

use League\OAuth2\Server\Repositories\ClientRepository;
use League\OAuth2\Server\Repositories\ScopeRepository;
use League\OAuth2\Server\Repositories\AccessTokenRepository;
use League\OAuth2\Server\Repositories\AuthCodeRepository;
use League\OAuth2\Server\Repositories\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Entities\UserEntity;

class ModuleOauth extends ModuleORM
{
    /**
     * Инициализация
     *
     */
    public function Init()
    {
        parent::Init();
        
    }
    
    /**
     * Получить объект oauth сервера
     */
    public function GetServer($sGrantType = 'authorization_code') {
        // Init our repositories
        $clientRepository = new ClientRepository(); // instance of ClientRepositoryInterface
        $scopeRepository = new ScopeRepository(); // instance of ScopeRepositoryInterface
        $accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
        

        $privateKey = $this->GetPrivateKeyPath();
        //$privateKey = new CryptKey($this->GetPrivateKeyPath(), 'passphrase'); // if private key has a pass phrase
        
        $encryptionKey = $this->GetEncryptionKey(); // generate using base64_encode(random_bytes(32))

        // Setup the authorization server
        $oServer = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );
        
        $this->EnableGrantType($oServer, $sGrantType);
        
        return $oServer;
    }
    
    public function EnableGrantType($oServer, $sGrantType) {
        
        if($sGrantType == 'authorization_code'){
            $this->EnableGrantTypeAuthCode($oServer);
        }
        
    }
    
    public function EnableGrantTypeAuthCode($oServer) {
        // Init our repositories
        $authCodeRepository = new AuthCodeRepository(); // instance of AuthCodeRepositoryInterface
        $refreshTokenRepository = new RefreshTokenRepository(); // instance of RefreshTokenRepositoryInterface
        
        /*
         * Создаем тип гранта
         */
        $oGrant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval(Config::Get('module.oauth.auth_code.expire')) // authorization codes will expire after
        );
        
        $oGrant->setRefreshTokenTTL(new \DateInterval(Config::Get('module.oauth.refresh_token.expire'))); // refresh tokens will expire after 

        // Enable the authentication code grant on the server
        $oServer->enableGrantType(
            $oGrant,
            new \DateInterval(Config::Get('module.oauth.access_token.expire')) // access tokens will expire after 
        );
        
    }
    
    public function GetPrivateKeyPath() {        
        return Config::Get('path.root.server').'/keys/private.key';
    }
    
    public function GetEncryptionKey() {
        
        $sPathEncryptionKey = Config::Get('path.root.server').'/keys/encryption.key';
        
        if(!$this->Fs_IsExistsFileLocal($sPathEncryptionKey)){
            return null;
        }
        
        return file_get_contents($sPathEncryptionKey);
    }
    
    public function GetUserEntity($oUser) {
        $eUser = new UserEntity;
        
        $eUser->setIdentifier($oUser->getId());
        $eUser->setEmail($oUser->getMail());
        
        return $eUser;        
    }

    public function GenerateState($iClientId, $sScopes = '') {
        $sKey = $iClientId . $sScopes . (new DateTime)->format('Y-m-d H:i:s');
        return md5($sKey);
    }
    
}