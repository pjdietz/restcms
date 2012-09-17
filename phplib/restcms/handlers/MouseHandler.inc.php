<?php

require_once('wellrested/Handler.inc.php');

class MouseHandler extends \wellrested\Handler {

    protected function get() {

        if (isset($this->args['mouseId'])) {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a mouse';
        } else {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a list of mice';
        }

    }

    protected function post() {

        if (!isset($this->args['mouseId'])) {
            $this->response->statusCode = 201;
            $this->response->setHeader('Location', '/mouse/12345');
        } else {
            $this->response->statusCode = 405;
        }

    }

}

?>
