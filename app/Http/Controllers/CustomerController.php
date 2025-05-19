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
        $results = DB::table('TBLCUSTOMER')
            ->where('CUSTNAME', 'like', '%' . $term . '%')
            ->orWhere('CUSTCODE', 'like', '%' . $term . '%')
            ->limit(20)
            ->get(['CUSTCODE as custcode', 'CUSTNAME as label', 'CUSTNAME as value', 'CUSTTYPE as custtype']);
        return response()->json($results);
    }
    
    /**
     * Look up customer information by customer code.
     */
    public function lookupByCode(Request $request)
    {
        $code = $request->get('code');
        
        $customer = DB::table('TBLCUSTOMER')
            ->where('CUSTCODE', $code)
            ->first(['CUSTCODE as custcode', 'CUSTNAME as custname', 'CUSTTYPE as custtype']);
            
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
        $customers = DB::table('TBLCUSTOMER')->orderByDesc('created_at')->paginate(25);
        return view('admin.customers', compact('customers'));
    }
    
    /**
     * Update customer contact information (phone and email).
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        // Update the customer record
        DB::table('TBLCUSTOMER')
            ->where('id', $id)
            ->update([
                'CONTACTCELLNUMBER' => $request->phone,
                'EMAIL' => $request->email,
                'updated_at' => now()
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Customer contact information updated successfully!'
        ]);
    }
}
