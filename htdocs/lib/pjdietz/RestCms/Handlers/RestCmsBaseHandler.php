<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\config;
use pjdietz\RestCms\Controllers\UserController;
use pjdietz\RestCms\Models\UserModel;
use pjdietz\RestCms\RestCmsCommonInterface;
use pjdietz\WellRESTed\Handler;

abstract class RestCmsBaseHandler extends Handler implements RestCmsCommonInterface
{
    /** @var UserModel */
    protected $user;

    protected function readUser($requireUser = false)
    {

        // No Authorization header
        if ($this->request->getHeader('Authorization') !== 'restcms') {

            // Fail if the handler requires a user.
            if ($requireUser) {
                $this->respondWithAuthenticationError();
            }

            // Return here. Any handler not needing a user can skip validating.
            return;

        }

        // Read the authorization scheme.
        $auth = $this->request->getHeader('X-restcms-auth');

        if ($auth === false) {
            // Wrong authentication scheme.
            $this->respondWithAuthenticationError();
        }

        // Parse the header into key-value pair.
        $authFields = self::parsePairs($auth);

        // Use the type of authentication determined by the configuration.
        if (config\AUTH_USE_REQUEST_HASH) {
            $this->authenticateUserWithRequestHash($authFields);
        } else {
            $this->authenticateUserWithPasswordHash($authFields);
        }

    }

    /**
     * Repond with 401 or 403 errors if the user is not supplied or not allowed.
     * If execution continues beyond this method, all is good.
     *
     * @param array|int $privileges
     */
    protected function assertUserPrivileges($privileges)
    {
        if (!isset($this->user)) {
            $this->readUser(true);
        }

        if (!$this->user->hasPrivileges($privileges)) {
            $this->respondWithForbiddenError();
        }
    }

    protected function respondWithAuthenticationError()
    {

        // TODO Additional message
        $this->response->setStatusCode(401);
        $this->response->setHeader('WWW-Authenticate', 'restcms');

        $body = "Please provide proper credentials for the request in the form of a X-restcms-auth header with a value in the following format:\n";

        if (config\AUTH_USE_REQUEST_HASH) {
            $body .= "username={your username}; requestHash={this request's hash}\n\n";
            $body .= "To make the request hash, concatenate the following and SHA256 the result: username, passwordHash, request URI, request method, request body.\n\n";
        } else {
            $body .= "username={your username}; passwordHash={your password hash}\n\n";
        }

        $this->response->setBody($body);

        $this->response->respond();
        exit;

    }

    protected function respondWithForbiddenError()
    {
        $this->response->setStatusCode(403);
        $this->response->respond();
        exit;
    }


    public static function parsePairs($str, $pairDelimiter = ';', $kvDelimiter = '=')
    {

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

    protected function authenticateUserWithRequestHash($authFields)
    {

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

        $user = UserController::newFromUsername($username);
        if ($user === false) {
            $this->respondWithAuthenticationError();
        } else {
            $this->user = $user;
        }

        if ($this->buildRequestHash() !== $requestHash) {
            $this->respondWithAuthenticationError();
        }

    }

    protected function buildRequestHash()
    {
        $str = $this->user->data['username'];
        $str .= $this->user->data['passwordHash'];
        $str .= $_SERVER['REQUEST_URI'];
        $str .= $this->request->getMethod();
        $str .= $this->request->getBody();
        return hash('sha256', $str);
    }

    private function authenticateUserWithPasswordHash($authFields)
    {
        if (!isset($authFields['username']) || $authFields['username'] === '') {
            $this->respondWithAuthenticationError();
            exit;
        }

        if (!isset($authFields['passwordHash']) || $authFields['passwordHash'] === '') {
            $this->respondWithAuthenticationError();
            exit;
        }

        $username = $authFields['username'];
        $passwordHash = $authFields['passwordHash'];
        $user = UserModel::newByCredentials($username, $passwordHash);

        if (is_null($user)) {
            $this->respondWithAuthenticationError();
        }

        $this->user = $user;

    }

    /**
     * @param object $validator
     * @param string $schemaUrl
     */
    protected function respondWithInvalidJsonError($validator, $schemaUrl)
    {
        // The request body was malformed or did not adhere to the schema.
        $this->response->setStatusCode(400);
        $this->response->setHeader('Content-type', 'text/plain');
        $body = "The request body was malformed or did not adhere to the schema.\n";
        $body .= "Schema for validation: " . $schemaUrl . "\n\n";
        $body .= "Violations:\n";
        foreach ($validator->getErrors() as $error) {
            $body .= sprintf("[%s] %s\n", $error['property'], $error['message']);
        }
        $this->response->setBody($body);
        $this->response->respond();
        exit;
    }

}
