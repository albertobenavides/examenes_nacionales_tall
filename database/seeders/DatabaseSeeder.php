<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (User::all() as $u) {
            if ($u->id != 1 && $u->id != 2) {
                DB::table('model_has_roles')->insert([
                    [
                        'role_id' => $u->rol_id,
                        'model_type' => 'App\Models\User',
                        'model_id' => $u->id
                    ]
                ]);
            }
        }
    }
}
