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
            ->orWhere('custcode', 'like', '%' . $term . '%')
            ->limit(20)
            ->get(['custcode', 'custname as label', 'custname as value', 'custtype']);
        return response()->json($results);
    }
    
    /**
     * Look up customer information by customer code.
     */
    public function lookupByCode(Request $request)
    {
        $code = $request->get('code');
        
        $customer = DB::table('tblcustomer')
            ->where('custcode', $code)
            ->first(['custcode', 'custname', 'custtype']);
            
        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }
    
    public function index()
    {
        $customers = DB::table('tblcustomer')->orderByDesc('created_at')->paginate(25);
        return view('admin.customers', compact('customers'));
    }
}
