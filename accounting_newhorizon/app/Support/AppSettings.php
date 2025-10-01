<?php

namespace App\Support;

use App\Models\Setting;

class AppSettings
{
    public static function get(string $name, $default = null)
    {
        $s = Setting::where('SettingName', $name)->where('isActive', true)->first();
        if (!$s) return $default;
        $val = $s->SettingValue;
        $type = strtolower((string)$s->DataType);
        return match ($type) {
            'bool', 'boolean' => in_array(strtolower($val), ['1', 'true', 'yes', 'on'], true),
            'int', 'integer'  => (int)$val,
            'float', 'double', 'decimal' => (float)$val,
            default => $val,
        };
    }
}
