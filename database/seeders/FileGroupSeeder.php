<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FileGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\FileGroup::create([
            'name' => 'General',
            'description' => 'The default file group.',
        ]);
    }
}
