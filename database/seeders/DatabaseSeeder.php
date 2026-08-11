<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(InitialSiteSeeder::class);

        // Keep tests focused on the baseline site fixture.
        if (! app()->environment('testing')) {
            $this->call(BulkCrudSeeder::class);
        }
    }
}
