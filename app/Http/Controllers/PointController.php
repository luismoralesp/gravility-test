<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Point;
use App\Http\Controllers\Controller;

/**
 * Description of PointController
 *
 * @author USUARIO
 */
class PointController extends Controller {
    /**
     * calculates the sum of the value of blocks whose x coordinate is between x1 and x2 (inclusive),
     * y coordinate between y1 and y2 (inclusive) and z coordinate between z1 and z2 (inclusive)
     * for a matriz.
     * 
     * @param int $x1
     * @param int $y1
     * @param int $z1
     * @param int $x2
     * @param int $y2
     * @param int $z2
     * @param int $matriz
     * @return int
     */
    public function query($x1, $y1, $z1, $x2, $y2, $z2, $matriz) {
        $sum = Point::where('x', '>=', $x1)
                ->where('x', '<=', $x2)
                ->where('y', '>=', $y1)
                ->where('y', '<=', $y2)
                ->where('z', '>=', $z1)
                ->where('z', '<=', $z2)
                ->where('matriz_id', $matriz)
            ->sum('value');
        return $sum;
    }
    
    /**
     * create or update a point in a matriz
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $value
     * @param int $matriz
     * @return Point|boolean
     */
    public function create_or_update($x, $y, $z, $value, $matriz) {
        $point = Point::where('x', '=', $x)
                    ->where('y', '=', $y)
                    ->where('z', '=', $z)
                    ->where('matriz_id', $matriz)
            ->first();
        if (!$point) {
            $point = new Point();
            $point->x = $x;
            $point->y = $y;
            $point->z = $z;
            $point->matriz_id = $matriz;
        }
        $point->value = $value;
        if ($point->saveOrFail()){
           return $point;
        }
        return false;
    }

}
