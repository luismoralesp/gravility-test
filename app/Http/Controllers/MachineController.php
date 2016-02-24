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
        if (count($lines) > 2) {
            $T = $lines[0];
            if (!is_numeric($T)) {
                throw new CoherenceException("Error en la linea 0: se esperaba un número para el paramétro 1, '$T' recivido");
            } else {
                $line_num = 0;
                for ($i = 0; $i < (int) $T; $i++) {
                    $line_num++;
                    $line_num = $this->run_testcase($line_num, $lines);
                }

                return TRUE;
            }
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
                throw new CoherenceException("Error en la linea $line_num: se esperaba un número para el paramétro 1, '$N' recivido");
            } else
            if (!is_numeric($M)) {
                throw new CoherenceException("Error en la linea $line_num: se esperaba un número para el paramétro 2, '$M' recivido");
            } else {
                $matriz = $this->matrzc->create($N, $N, $N);
                for ($i = 0; $i < (int) $M; $i++) {
                    $line_num++;
                    $this->run_query_or_update($line_num, $lines[$line_num], $matriz->id, $N);
                }
            }
        } else {
            throw new CoherenceException("Error en la linea $line_num: se esperabas 2 parámetros $count recivido(s).");
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
                throw new CoherenceException("Error en la line $line_num: se esperaba QUERY o UPDATE '" . $words[0] . "' recivido.");
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
                    throw new CoherenceException("Error en la linea $line_num: se esperaba un número entre 1 y $N para el paramétro $i, '" . $words[$i] . "' recivido");
                }
            }
            $sum = $this->pointc->query($words[1], $words[2], $words[3], $words[4], $words[5], $words[6], $matriz);
            $this->output .= "$sum\n";
        } else {
            throw new CoherenceException("Error en la linea $line_num: se esperaban 7 parametros '$count' recividos");
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
                if (!is_numeric($words[$i]) || ((int) $words[$i]) > ((int) $N) || ((int) $words[$i]) < 1) {
                    throw new CoherenceException("Error en la linea $line_num: se esperaba un número entre 1 y $N para el paramétro $i, '" . $words[$i] . "' recivido");
                }
            }
            if (!is_numeric($words[4])) {
                throw new CoherenceException("Error en la linea $line_num: se esperaba un número para el paramétro 4, '" . $words[4] . "' recivido");
            }
            $this->pointc->create_or_update($words[1], $words[2], $words[3], $words[4], $matriz);
        } else {
            throw new CoherenceException("Error en la linea $line_num: se esperaban 5 parametros '$count' recividos");
        }
    }
    public function getOutput() {
        return $this->output;
    }
}
