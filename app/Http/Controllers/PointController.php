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
        if ($point->saveOrFail()) {
            return $point;
        }
        return false;
    }

    /*     * *
     * la clase servicio no respeta el encapsulamiento de atributos
     * es preferible usar constantes para los valores comprativos en ves de valores que facilite su cambio posterior
     * el metodo es muy extenso es preferible dividir sus funcionalidades en metodos emenos complejos para facilitar su depuracion
     * el mismo metodo con los mismos parametros es invocado màs de una vez, es preferible evitar esto
     */

    public function post_confirm2() {
        $id = Input::get('service_id');
        $servicio = Servicio::find($id);
        if ($servicio != NULL) {
            if ($servicio->status_id == '6') {
                return Response::json(array('error' => '6'));
            }
            if ($servicio->driver_id == NULL && $servicio->status_id = '1') {
                $servicio = Service::update($id, array(
                            'dirver_id' => Input::get('driver_id'),
                            'status_id' => '2'
                ));
                Driver::update(Input::get('driver_id'), array(
                    "available" => '0'
                ));
                $driverTmp = Driver::find(Input::get('dirver_id'));
                Service::update($id, array(
                    'car_id' => $driverTmp->car_id
                ));
                $pushMenssaje = 'Tu servicio ha sido confirmado';
                $servicio = Service::find($id);
                $push = Push::make();
                if ($servicio->user->uuid === '') {
                    return Response::json(array('error' => '0'));
                }
                if ($servicio->user->type === '1') {
                    $result = $push->ios($servicio->user->uuid, $pushMenssaje, 1, 'honk.wave', 'Open', array('serviceId' => $servicio->id));
                } else {
                    $result = $push->android2($servicio->user->uuid, $pushMenssaje, 1, 'default', 'Open', array('serviceId' => $servicio->id));
                }
                return Response::json(array('error' => '0'));
            } else {
                return Response::json(array('error' => '1'));
            }
        } else {
            return Response::json(array('error' => '3'));
        }
    }

    /**
     * los valores para comprara fueron definidos como variables constantes
     */
    const ERROR_0 = '0';
    const ERROR_1 = '1';
    const ERROR_3 = '3';
    const ERROR_6 = '6';
    const STATUS_1 = '1';
    const STATUS_2 = '2';
    const STATUS_6 = '6';
    const AVIABLE_0 = '0';
    const MESSAGE = 'Tu servicio ha sido confirmado';
    const TYPE_1 = '1';
    const NON_UUID = '';
    const IOS = 'honk.wave';
    const ANDROID = 'default';
    const OPEN = 'open';
    const ONE = 1;

    public function post_confirm() {
        $service_id = Input::get('service_id'); //el valor del $service_id se obtiene una unica vez
        $servicio = Servicio::find($service_id); //el valor del $servicio se obtiene una unica vez
        if ($servicio != NULL) {
            if ($servicio->status_id === self::STATUS_1 && $servicio->dirver_id = NULL) {//La funcionalidad pricipal se evalua primero
                return push_message($service_id);
            }else//Luego se evaluan los errores
            if ($servicio->status_id === self::STATUS_6) {
                return Response::json(array('error' => self::ERROR_6));
            }
            return Response::json(array('error' => self::ERROR_1));
        }
        return Response::json(array('error' => self::ERROR_3));
    }

    /**
     * La funcionalidad se separo en un meodo diferente para facilitar su depuración
     * @param type $service_id
     * @return type
     */
    public function push_message($service_id) {
        $driver_id = Input::get('driver_id');//elvalor del $driver_id se obtiene una única vez
        $servicio = Servicio::update($service_id, array(
                    'diver_id' => $driver_id,
                    'status_id' => self::STATUS_2
        ));
        $driver = Driver::update($driver_id, array(
                    'available' => self::AVIABLE_0
        ));
        Service::update($service_id, array(
            'car_id' => $driver->car_id
        ));
        $push = Push::make();
        if ($servicio->user->uuid != self::NON_UUID) {
            if ($servicio->user->type === self::TYPE_1) {
                $push->ios($servicio->user->uuid, self::MESSAGE, self::ONE, self::IOS, self::OPEN, array('serviceId' => $service_id));
            } else {
                $push->android2($servicio->user->uuid, self::MESSAGE, self::ONE, self::ANDROID, self::OPEN, array('serviceId' => $service_id));
            }
        }
        return Response::json(array('error' => self::ERROR_0));
    }

}
