<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Return a list of customer names matching the search term for autocomplete.
     */
    public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $results = DB::table('tblcustomer')
            ->where('custname', 'like', '%' . $term . '%')
            ->limit(20)
            ->get(['custname as label', 'custname as value', 'custtype']);
        return response()->json($results);
    }
}
