<?php

use App\Http\Controllers\MachineController;
use App\Exceptions\CoherenceException;

class MachineTest extends TestCase {

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_check_sintaxis_ok() {
        $contr = new MachineController();
        $input = "1\n1 1\nUPDATE 1 1 1 1\nQUERY 1 1 1 1 1 100\n1 1\nUPDATE 1 1 1 1\n";
        $result = $contr->check_sintaxis($input);
        $this->assertEquals(TRUE, $result);
    }

    public function test_check_sintaxis_error() {
        $contr = new MachineController();
        $input = "1\n1 1\nUPDATE 1 1 1 1\nQUERY 1 1 1 1 1 100\n1 1\nUPDATE2 1 1 1 1\n";
        $result = $contr->check_sintaxis($input);
        $this->assertEquals(FALSE, $result);
    }

    public function test_run_machine_ok() {
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
        $result = $contr->run_machine($input);
        echo $contr->getOutput();
        $this->assertEquals(TRUE, $result);
    }

    public function test_run_machine_few_data() {
        $contr = new MachineController();
        $input = "";
        try {
            $result = $contr->run_machine($input);
            $this->assertEquals(FALSE, $result);
        } catch (CoherenceException $ex) {
            var_dump($ex->getMessage());
            $this->assertEquals(MachineController::FEW_DATA, $ex->getCode());
        }
    }

    public function test_run_machine_type_error() {
        $contr = new MachineController();
        $input = "";
        $input .= "2\n";
        $input .= "4 f\n";
        try {
            $result = $contr->run_machine($input);
            $this->assertEquals(FALSE, $result);
        } catch (CoherenceException $ex) {
            var_dump($ex->getMessage());
            $this->assertEquals(MachineController::TYPE_ERROR, $ex->getCode());
        }
    }

    public function test_run_machine_few_or_many() {
        $contr = new MachineController();
        $input = "";
        $input .= "2\n";
        $input .= "4 2 4\n";
        try {
            $result = $contr->run_machine($input);
            $this->assertEquals(FALSE, $result);
        } catch (CoherenceException $ex) {
            var_dump($ex->getMessage());
            $this->assertEquals(MachineController::FEW_OR_MANY_PARAMS, $ex->getCode());
        }
    }


    public function test_run_machine_wrong_command() {
        $contr = new MachineController();
        $input = "";
        $input .= "2\n";
        $input .= "4 2\n";
        $input .= "DELETE 2 2 2 4\n";
        try {
            $result = $contr->run_machine($input);
            $this->assertEquals(FALSE, $result);
        } catch (CoherenceException $ex) {
            var_dump($ex->getMessage());
            $this->assertEquals(MachineController::WRONG_COMMAND, $ex->getCode());
        }
    }


    public function test_run_machine_outofboundary() {
        $contr = new MachineController();
        $input = "";
        $input .= "2\n";
        $input .= "4 2\n";
        $input .= "UPDATE 2 2 2 4000000000000000\n";
        try {
            $result = $contr->run_machine($input);
            $this->assertEquals(FALSE, $result);
        } catch (CoherenceException $ex) {
            var_dump($ex->getMessage());
            $this->assertEquals(MachineController::OUTOFBOUNDARY, $ex->getCode());
        }
    }


}
