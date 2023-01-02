<?php

namespace Tests\Unit;

use App\Rules\SortableColumn;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortableColumnTest extends TestCase
{
    /** @test */
    function validates_sortable_values()
    {
        $rule = new SortableColumn(['first_name', 'email', 'date', 'id']);

        $this->assertTrue($rule->passes('order', 'id'));
        $this->assertTrue($rule->passes('order', 'id-desc'));
        $this->assertTrue($rule->passes('order', 'first_name'));
        $this->assertTrue($rule->passes('order', 'email'));
        $this->assertTrue($rule->passes('order', 'date'));
        $this->assertTrue($rule->passes('order', 'first_name-desc'));
        $this->assertTrue($rule->passes('order', 'email-desc'));

        $this->assertFalse($rule->passes('order', []));
        $this->assertFalse($rule->passes('order', 'first_name-descendent'));
        $this->assertFalse($rule->passes('order', 'asc-name'));
        $this->assertFalse($rule->passes('order', 'name'));
        $this->assertFalse($rule->passes('order', 'email-descx'));
        $this->assertFalse($rule->passes('order', 'desc-first_name'));
    }
}
