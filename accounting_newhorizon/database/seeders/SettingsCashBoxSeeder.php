<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsCashBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Setting::updateOrCreate(
            ['SettingName' => 'cashboxes.auto_create_child_account'],
            ['SettingValue' => '1', 'DataType' => 'bool', 'isActive' => true]
        );

        Setting::updateOrCreate(
            ['SettingName' => 'cashboxes.parent_account_id'],
            ['SettingValue' => '123', 'DataType' => 'int', 'isActive' => true] // غيّر 123
        );
    }
}
