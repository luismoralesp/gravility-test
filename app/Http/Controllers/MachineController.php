<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MatrizController;
use App\Http\Controllers\PointController;
use App\Exceptions\CoherenceException;

/**
 * Description of MachineController
 *
 * @author Luis Miguel Morales Pajaro
 */
class MachineController extends Controller {

    /**
     * 
     * Threw when have no inserted enough data, the minimun lines of data that needs the machine to work is 2.
     */
    const FEW_DATA = 1001;

    /**
     * 
     * Threw when have insterted a wrong data type.
     */
    const TYPE_ERROR = 1002;

    /**
     * 
     * Threw when have inserted wrong number of paramter 
     */
    const FEW_OR_MANY_PARAMS = 1003;

    /**
     * 
     * Threw when have inserted an unknow command, only QUERY and UPDATE are currently acepted.
     */
    const WRONG_COMMAND = 1004;

    /**
     * 
     * Threw when an inserted value is out of acepted boundaries.
     */
    const OUTOFBOUNDARY = 1005;

    private $matrzc;
    private $pointc;
    private $output;

    public function __construct() {
        $this->matrzc = new MatrizController();
        $this->pointc = new PointController();
    }

    /**
     * check input sintaxis
     * @param string $input
     * @return boolean
     */
    public function check_sintaxis($input) {
        $pattern = "/^\d+\n(\d+ \d+\n(UPDATE \d+ \d+ \d+ \d+\n|QUERY \d+ \d+ \d+ \d+ \d+ \d+\n)*)*$/";
        $result = preg_match($pattern, $input);
        return $result > 0;
    }

    /**
     * Excecute the machine for inpterprete the input text
     * @param string $input
     * @return boolean
     * @throws CoherenceException
     */
    public function run_machine($input) {
        $lines = explode("\n", $input);
        $this->output = "";
        if (count($lines) >= 2) {
            $T = $lines[0];
            if (!is_numeric($T)) {
                throw new CoherenceException("Error en la linea 1: se esperaba un número para el paramétro 1, '$T' recivido", self::TYPE_ERROR, 0);
            }else 
            if (((int) $T) < 1 || ((int) $T) > 50) {
                throw new CoherenceException("Error en la linea 1: se esperaba un número entre 1 y 50 para el paramétro 1, '$T' recivido", self::OUTOFBOUNDARY, 0);
            } else {
                $line_num = 0;
                for ($i = 0; $i < (int) $T; $i++) {
                    $line_num++;
                    $line_num = $this->run_testcase($line_num, $lines);
                }

                return TRUE;
            }
        } else {
            throw new CoherenceException("Error en la linea 1: hay muy pocos datos", self::FEW_DATA, 0);
        }
        return FALSE;
    }

    /**
     * run a test case interpreted in the text input 
     * @param int $line_num
     * @param array $lines
     * @return int
     * @throws CoherenceException
     */
    protected function run_testcase($line_num, $lines) {
        $words = explode(" ", $lines[$line_num]);
        $count = count($words);
        if ($count === 2) {
            $N = $words[0];
            $M = $words[1];

            if (!is_numeric($N)) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número para el paramétro 1, '$N' recivido", self::TYPE_ERROR, $line_num);
            } else
            if (((int) $N) < 1 || ((int) $N) > 100) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número entre 1 y 100 para el paramétro 1, '$N' recivido", self::OUTOFBOUNDARY, $line_num);
            } else 
            if (!is_numeric($M)) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número para el paramétro 2, '$M' recivido", self::TYPE_ERROR, $line_num);
            } else 
            if (((int) $M) < 1 || ((int) $M) > 1000) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número entre 1 y 1000 para el paramétro 2, '$M' recivido", self::OUTOFBOUNDARY, $line_num);
            } else {
                $matriz = $this->matrzc->create($N, $N, $N);
                for ($i = 0; $i < (int) $M; $i++) {
                    $line_num++;
                    if (count($lines) > $line_num){
                        $this->run_query_or_update($line_num, $lines[$line_num], $matriz->id, $N);
                    }else{
                        throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba una linea más", self::FEW_DATA, $line_num);
                    }
                }
            }
        } else {
            throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperabas 2 parámetros $count recivido(s).", self::FEW_OR_MANY_PARAMS, $line_num);
        }
        return $line_num;
    }

    /**
     * interprete and run the command if is a QUERY or an UPDATE
     * @param int $line_num
     * @param string $line
     * @throws CoherenceException
     */
    protected function run_query_or_update($line_num, $line, $matriz, $N) {
        $words = explode(" ", $line);
        if (count($words)) {
            if ($words[0] == 'QUERY') {
                $this->run_query($line_num, $words, $matriz, $N);
            } else
            if ($words[0] == 'UPDATE') {
                $this->run_update($line_num, $words, $matriz, $N);
            } else {
                throw new CoherenceException("Error en la line " . ($line_num + 1) . ": se esperaba QUERY o UPDATE '" . $words[0] . "' recivido.", self::WRONG_COMMAND, $line_num);
            }
        }
    }

    /**
     * run a QUERY
     * @param array $words
     */
    protected function run_query($line_num, $words, $matriz, $N) {
        $count = count($words);
        if ($count === 7) {
            for ($i = 1; $i <= 6; $i++) {
                if (!is_numeric($words[$i]) || ((int) $words[$i]) > ((int) $N) || ((int) $words[$i]) < 1) {
                    throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número entre 1 y $N para el paramétro $i, '" . $words[$i] . "' recivido", self::OUTOFBOUNDARY, $line_num);
                }
            }
            $sum = $this->pointc->query($words[1], $words[2], $words[3], $words[4], $words[5], $words[6], $matriz);
            $this->output .= "$sum\n";
        } else {
            throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaban 7 parametros '$count' recividos", self::FEW_OR_MANY_PARAMS, $line_num);
        }
    }

    /**
     * run an UPDATE
     * @param array $words
     */
    protected function run_update($line_num, $words, $matriz, $N) {
        $count = count($words);
        if ($count === 5) {
            for ($i = 1; $i <= 3; $i++) {
                if (!is_numeric($words[$i])) {
                    throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número para el paramétro $i, '" . $words[$i] . "' recivido", self::TYPE_ERROR, $line_num);
                }else 
                if (((int) $words[$i]) > ((int) $N) || ((int) $words[$i]) < 1) {
                    throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número entre 1 y $N para el paramétro $i, '" . $words[$i] . "' recivido", self::OUTOFBOUNDARY, $line_num);
                }
            }
            if (!is_numeric($words[4])) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número para el paramétro 4, '" . $words[4] . "' recivido", self::TYPE_ERROR, $line_num);
            }else
            if (((int) $words[4]) > pow(10, 9)|| ((int) $words[4]) < -pow(10, 9)) {
                throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaba un número entre -1000000000 y 1000000000 para el paramétro 4, '" . $words[4] . "' recivido", self::OUTOFBOUNDARY, $line_num);
            }
            $this->pointc->create_or_update($words[1], $words[2], $words[3], $words[4], $matriz);
        } else {
            throw new CoherenceException("Error en la linea " . ($line_num + 1) . ": se esperaban 5 parametros '$count' recividos", self::FEW_OR_MANY_PARAMS, $line_num);
        }
    }

    public function getOutput() {
        return $this->output;
    }

}
