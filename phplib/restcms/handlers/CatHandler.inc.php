<?php

require_once('wellrested/Handler.inc.php');

class CatHandler extends \wellrested\Handler {

    protected function get() {

        if (isset($this->matches['catId'])) {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a cat';
        } else {
            $this->response->statusCode = 200;
            $this->response->body = 'It is a list of cats';
        }

    }

}

?>
