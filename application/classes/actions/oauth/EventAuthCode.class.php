<?php


/**
 * Description of EventAuthCode
 *
 * @author oleg
 */
class ActionOauth_EventAuthCode extends Event {
    
    public function EventAuth() {

        $oServer = $this->Oauth_GetServer('authorization_code');
        
        $oRequest = new Psr\Http\Message\RequestPSR7;
        $oRequest->withQueryParam('response_type', 'code');
        print_r( $oRequest->getQueryParams());
        
        try {
    
            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $oServer->validateAuthorizationRequest($oRequest);

            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser(new UserEntity()); // an instance of UserEntityInterface

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $server->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {
            echo $exception->getMessage();
            // Unknown exception
//            $body = new (fopen('php://temp', 'r+'));
//            $body->write($exception->getMessage());
//            return $response->withStatus(500)->withBody($body);

        }
        
    }
}
