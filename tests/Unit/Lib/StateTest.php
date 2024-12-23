<?php

namespace Tests\Unit\Lib;

use Lib\State;
use Tests\TestCase;

class StateTest extends TestCase
{
    public function test_has_all_states(): void
    {
        $this->assertEquals(27, count(State::getStates()));
    }

    public function test_all_states_have_two_letters(): void
    {
        $states = State::getStates();

        foreach ($states as $state) {
            $this->assertEquals(2, strlen($state));
        }
    }

    public function test_all_states_are_uppercase(): void
    {
        $states = State::getStates();

        foreach ($states as $state) {
            $this->assertEquals(strtoupper($state), $state);
        }
    }
}
