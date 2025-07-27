<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIssue extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    public static function get_new_code()
    {
        $year_now = date('Y');
        $item = self::selectRaw("MAX(id) AS max")
            ->whereYear('created_at', $year_now)
            ->first();
        $max = ($item->max != null) ? substr($item->max, -5) : 0;
        $increment = (int)$max + 1;

        $str = str_pad($increment, 5, '0', STR_PAD_LEFT);
        $new_code = "PO/" . $year_now . "/" . $str;

        return $new_code;
    }
}
