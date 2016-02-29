<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Exceptions;

/**
 * Description of CoherenceException
 *
 * @author Luis Miguel Morales Pajaro
 */
class CoherenceException extends \Exception {
    
    
    public function __construct($message = "", $code = 0, $line = 0) {
        parent::__construct($message, $code);
        $this->line = $line;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
