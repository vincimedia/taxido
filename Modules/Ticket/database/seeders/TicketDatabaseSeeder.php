<?php

namespace Modules\Ticket\Database\Seeders;

use Illuminate\Database\Seeder;

class TicketDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(FormFieldsSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PrioritySeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(StatusSeeder::class);
    }
}
