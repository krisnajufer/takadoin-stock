<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockLedgerEntry;
use Illuminate\Support\Facades\DB;

class PatchController extends Controller
{
    public function repair_sle()
    {
        $items = Item::where('is_material', 1)->get();
        
        foreach ($items as $key => $data) {
            $total = 0;
            echo("=============================================================". $data->id);
            echo("<br>");
            $sle = StockLedgerEntry::where('item_id', $data->id)
            ->orderBy('posting_date', 'ASC')
            ->orderBy('posting_time', 'ASC')
            ->orderBy('created_at', 'ASC')->get();
            foreach ($sle as $key => $row_sle) {
                $total += $row_sle->qty_change;
                StockLedgerEntry::where('id', $row_sle->id)->update(['qty_after_transaction' => $total]);
                echo($row_sle->item_id ." | ". $row_sle->qty_change. " | ". $row_sle->qty_after_transaction . " | " . $total ."<br>");
            }
        }
    }
}
