<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    public static function rules()
    {
        return [
            "name" => "required|unique:items,name|max:255"
        ];
    }

    public static function get_new_code($is_material = false)
    {
        $year_now = date('Y');
        $item = self::selectRaw("MAX(id) AS max")
            ->where('is_material', $is_material)
            ->whereYear('created_at', $year_now)
            ->first();
        $max = ($item->max != null) ? substr($item->max, -5) : 0;
        $increment = (int)$max + 1;

        $type = $is_material ? "MTR" : "ITM";
        $str = str_pad($increment, 5, '0', STR_PAD_LEFT);
        $new_code = $type . "/" . $year_now . "/" . $str;

        return $new_code;
    }
}
