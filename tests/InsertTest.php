<?php

namespace Imanghafoori\EloquentMockery\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\EloquentMockery\FakeDB;
use PHPUnit\Framework\TestCase;

class InsertyUser extends Model
{
    //
}

class InsertTest extends TestCase
{
    public function setUp(): void
    {
        FakeDB::mockQueryBuilder();
    }

    public function tearDown(): void
    {
        FakeDB::dontMockQueryBuilder();
    }

    /**
     * @test
     */
    public function insertBasicTest()
    {
        $res1 = InsertyUser::query()->insert(['name' => 'Hello', 'age' => 20,]);
        $res2 = InsertyUser::query()->insert(['id' => 2, 'name' => 'Iman 2', 'age' => 30,]);
        // can set the "id" manually.
        $res3 = InsertyUser::query()->insert(['id' => 6, 'name' => 'Iman 3', 'age' => 66,]);
        // next row will continue on the latest and greatest id.
        $res4 = InsertyUser::query()->insert(['name' => 'Bye', 'age' => 77,]);

        $users = InsertyUser::query()->whereKey(1)->get();
        $this->assertEquals(1, ($users[0])->getKey());
        $this->assertEquals('Hello', ($users[0])->name);

        $count = InsertyUser::query()->count();
        $this->assertEquals(4, $count);

        $users = InsertyUser::query()->find(2);
        $this->assertNotNull($users);

        // check the gap in Ids are empty.
        $this->assertNull(InsertyUser::find(3));
        // check id 6
        $user = InsertyUser::query()->find(6);
        $this->assertNotNull($user);
        $this->assertEquals(66, $user->age);
        // check id 7
        $user = InsertyUser::query()->find(7);
        $this->assertNotNull($user);
        $this->assertEquals(77, $user->age);

        $this->assertTrue($res3);
        $this->assertTrue($res4);
    }

    /**
     * @test
     */
    public function insertMass()
    {
        InsertyUser::query()->insert([
            ['value' => 1, 'deleted_at' => null],
            ['value' => 2, 'deleted_at' => '2021-12-01 00:00:00'],
            ['value' => 3, 'deleted_at' => null],
            ['value' => 4, 'deleted_at' => '2021-12-02 00:00:00'],
        ]);

        $users1 = InsertyUser::query()->find(1);
        $users2 = InsertyUser::query()->find(2);
        $users3 = InsertyUser::query()->find(3);
        $users4 = InsertyUser::query()->find(4);

        $this->assertEquals(1, $users1->value);
        $this->assertEquals(2, $users2->value);
        $this->assertEquals(3, $users3->value);
        $this->assertEquals(4, $users4->value);
    }
}
