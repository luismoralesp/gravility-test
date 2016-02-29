<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HttpTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function test_run()
    {
        $response = $this->call('POST','/run', ['text'=>'hola']);
        var_dump($response->getContent());
    }
    
    public function test_char_endline() {
        $input = "";
        $input .= "2\n";
        $input .= "4 5\n";
        $input .= "UPDATE 2 2 2 4\n";
        $input .= "QUERY 1 1 1 3 3 3\n";
        $input .= "UPDATE 1 1 1 23\n";
        $input .= "QUERY 2 2 2 4 4 4\n";
        $input .= "QUERY 1 1 1 3 3 3\n";
        $input .= "2 4\n";
        $input .= "UPDATE 2 2 2 1\n";
        $input .= "QUERY 1 1 1 1 1 1\n";
        $input .= "QUERY 1 1 1 2 2 2\n";
        $input .= "QUERY 2 2 2 2 2 2\n";
        $this->visit('/')
         ->type($input, 'text')
         ->press('button')
         ->seeJson([
             'error' => false
         ]);
    }
}
