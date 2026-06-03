<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\InitialSiteSeeder;

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
        // Seed initial Opportunet Mondiale
        $this->call(InitialSiteSeeder::class);
    }
}
