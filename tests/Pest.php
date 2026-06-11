<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

uses()->beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
})->in('Feature');
