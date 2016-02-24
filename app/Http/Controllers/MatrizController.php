<?php


namespace App\Http\Controllers;

use App\Matrix;
use App\Http\Controllers\Controller;

/**
 * Description of MatrizController
 *
 * @author Luis Miguel Morales Pajaro
 */
class MatrizController extends Controller{
    
    public function create($width, $height, $large) {
        $matriz = new Matrix();
        $matriz->width = $width;
        $matriz->height = $height;
        $matriz->large = $large;
        if ($matriz->saveOrFail()){
            return $matriz;
        }
        return false;
    }
}
