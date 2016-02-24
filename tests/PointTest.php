<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\PointController;

class PointTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_create_or_update()
    {
        $contr = new PointController();
        $point = $contr->create_or_update(1, 2, 3, 2, 3);
        $this->assertInstanceOf(App\Point::class, $point);
    }
    
    public function test_query() {
        $contr = new PointController();
        $sum = $contr->query(1, 1, 1, 3, 3, 3, 3);
        $this->assertEquals(TRUE, is_numeric($sum));
    }
}
