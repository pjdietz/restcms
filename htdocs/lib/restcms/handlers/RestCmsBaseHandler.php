<?php

namespace restcms\handlers;

abstract class RestCmsBaseHandler extends \pjdietz\WellRESTed\Handler {

    protected $user;

    public function __construct($request, $args=null) {
        parent::__construct($request, $args);
    }

    protected function readUser($requireUser=false) {

        // No Authorization header
        if ($this->request->getHeader('Authorization') !== 'restcms') {

            // Fail if the handler requires a user.
            if ($requireUser) {
                $this->respondWithAuthenticationError();
            }

            // Return here. Any handler not needing a user can skip validating.
            return;

        }

        // Read the authoziation scheme.
        $auth = $this->request->getHeader('X-restcms-auth');

        if ($auth === false) {
            // Wrong authentication sheme.
            $this->respondWithAuthenticationError();
        }

        // Parse the header into key-value pair.
        $authFields = self::parsePairs($auth);

        // Use the type of authentication determined by the configuration.
        if (\restcms\config\AUTH_USE_REQUEST_HASH) {
            $this->authenticateUserWithRequestHash($authFields);
        } else {
            $this->authenticateUserWithPasswordHash($authFields);
        }

    }

    protected function authenticateUserWithPasswordHash($authFields) {

        if (isset($authFields['username']) && $authFields['username'] !== '') {
            $username = $authFields['username'];
        } else {
            $this->respondWithAuthenticationError();
            exit;
        }

        $user = \restcms\controllers\UserController::newFromUsername($username);
        if ($user === false) {
            $this->respondWithAuthenticationError();
        } else {
            $this->user = $user;
        }

        if (isset($authFields['passwordHash']) && $authFields['passwordHash'] !== '') {
            $passwordHash = $authFields['passwordHash'];
        } else {
            $this->respondWithAuthenticationError();
            exit;
        }

        if ($passwordHash !== $user->data['passwordHash']) {
            $this->respondWithAuthenticationError();
            exit;
        }

    }

    protected function authenticateUserWithRequestHash($authFields) {

        if (isset($authFields['requestHash']) && $authFields['requestHash'] !== '') {
            $requestHash = $authFields['requestHash'];
        } else {
            $this->respondWithAuthenticationError();
            exit;
        }

        if (isset($authFields['username']) && $authFields['username'] !== '') {
            $username = $authFields['username'];
        } else {
            $this->respondWithAuthenticationError();
            exit;
        }

        $user = \restcms\controllers\UserController::newFromUsername($username);
        if ($user === false) {
            $this->respondWithAuthenticationError();
        } else {
            $this->user = $user;
        }

        if ($this->buildRequestHash() !== $requestHash) {
            $this->respondWithAuthenticationError();
        }

    }

    protected function buildRequestHash() {

        $str = $this->user->data['username'];
        $str .= $this->user->data['passwordHash'];
        $str .= $_SERVER['REQUEST_URI'];
        $str .= $this->request->method;
        $str .= $this->request->body;
        return hash('sha256', $str);

    }

    protected function respondWithAuthenticationError() {

        // TODO Additional message
        $this->response->statusCode = 401;
        $this->response->setHeader('WWW-Authenticate', 'restcms');

        $this->response->body = "Please provide proper credentials for the request in the form of a X-restcms-auth header with a value in the following format:\n";

        if (\restcms\config\AUTH_USE_REQUEST_HASH) {

            $this->response->body .= "username={your username}; requestHash={this request's hash}\n\n";
            $this->response->body .= "To make the request hash, concatenate the following and SHA256 the result: username, passwordHash, request URI, request method, request body.\n\n";

        } else {

            $this->response->body .= "username={your username}; passwordHash={your password hash}\n\n";

        }

        $this->response->respond();
        exit;

    }

    public static function parsePairs($str, $pairDelimiter=';', $kvDelimiter='=') {

        $rtn = array();

        $pairs = explode($pairDelimiter, $str);

        foreach ($pairs as $pair) {

            @list($k, $v) = explode($kvDelimiter, $pair, 2);

            if (isset($k) && isset($v)) {
                $rtn[trim($k)] = trim($v);
            }

        }

        return $rtn;

    }

}

?>