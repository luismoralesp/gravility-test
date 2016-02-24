<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\MatrizController;

class MatrizTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_create()
    {
        $contr = new MatrizController();
        $result = $contr->create(10, 10, 10);
        $this->assertInstanceOf(\App\Matrix::class, $result);
    }
}
