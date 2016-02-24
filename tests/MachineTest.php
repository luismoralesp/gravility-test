<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\MachineController;

class MachineTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_check_sintaxis()
    {
        $contr = new MachineController();
        $input = "1\n1 1\nUPDATE 1 1 1 1\nQUERY 1 1 1 1 1 100\n1 1\nUPDATE 1 1 1 1\n";
        $result = $contr->check_sintaxis($input);
        $this->assertEquals(TRUE, $result);
    }
    
    public function test_run_machine() {
        $contr = new MachineController();
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
        //$input = "2\n2 2\nUPDATE 1 1 1 1\nQUERY 1 1 1 1 1 100\n4 1\nUPDATE 1 1 1 1\n";
        $result = $contr->run_machine($input);
        echo $contr->getOutput();
        $this->assertEquals(TRUE, $result);
    }
}
