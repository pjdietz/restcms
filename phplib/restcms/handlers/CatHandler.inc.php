<?php

require_once('wellrested/Handler.inc.php');

class CatHandler extends \wellrested\Handler {

    protected function get() {

        if (isset($this->args['catId'])) {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a cat';
        } else {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a list of cats';
        }

    }

    protected function post() {

        if (!isset($this->args['catId'])) {
            $this->response->statusCode = 201;
            $this->response->setHeader('Location', '/cat/12345');
        } else {
             $this->response->statusCode = 405;
        }

    }

}

?>
