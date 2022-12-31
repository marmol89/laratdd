<?php

namespace Tests\Unit;

use App\{User , Login};
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /** @test  */
    function gets_the_last_login_datetime_of_each_user()
    {
        $joel = factory(User::class)->create(['first_name' => 'Joel']);
        factory(Login::class)->create([
            'user_id' => $joel->id,
            'created_at' =>'2020-09-18 12:30:00',
        ]);
        factory(Login::class)->create([
            'user_id' => $joel->id,
            'created_at' =>'2020-09-18 12:31:00',
        ]);
        factory(Login::class)->create([
            'user_id' => $joel->id,
            'created_at' =>'2020-09-17 12:31:00',
        ]);

        $ellie = factory(User::class)->create(['first_name' => 'Ellie']);
        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' =>'2020-09-15 12:00:00',
        ]);
        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' =>'2020-09-15 12:01:00',
        ]);
        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' =>'2020-09-15 11:59:59',
        ]);

        $users = User::all();

        $this->assertEquals(Carbon::parse('2020-09-18 12:31:00') , $users->firstWhere('first_name' , 'Joel')->lastLogin->created_at);
        $this->assertEquals(Carbon::parse('2020-09-15 12:01:00') , $users->firstWhere('first_name' , 'Ellie')->lastLogin->created_at);
    }

}
