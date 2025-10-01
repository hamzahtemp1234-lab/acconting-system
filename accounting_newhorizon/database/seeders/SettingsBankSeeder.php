<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Setting::updateOrCreate(
            ['SettingName' => 'banks.auto_create_child_account'],
            ['SettingValue' => '1', 'DataType' => 'bool', 'isActive' => true]
        );

        Setting::updateOrCreate(
            ['SettingName' => 'banks.auto_create_child_account'],
            ['SettingValue' => '123', 'DataType' => 'int', 'isActive' => true] // غيّر 123
        );
    }
}
