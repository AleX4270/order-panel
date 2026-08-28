<?php
declare(strict_types=1);

namespace Tests;

use Database\Seeders\TestingSeeder;
use Illuminate\Foundation\Testing\Attributes\Seeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

#[Seeder(TestingSeeder::class)]
abstract class TestCase extends BaseTestCase {}
