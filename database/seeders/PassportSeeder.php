<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    $client = new ClientRepository();

    $client->createPasswordGrantClient(null, 'Default password grant client', 'http://your.redirect.path');
    $client->createPersonalAccessClient(null, 'Default personal access client', 'http://your.redirect.path');
    }
}
