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
        $validator = validator([
            'phone' => $request->phone,
            'email' => $request->email,
        ], [
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        // Check if phone number already exists for another customer
        $existingPhone = DB::table('TBLCUSTOMER')
            ->where('CONTACTCELLNUMBER', $request->phone)
            ->where('id', '!=', $id)
            ->first(['id', 'CUSTNAME']);
            
        if ($existingPhone) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'phone' => ['This phone number is already used by customer: ' . $existingPhone->CUSTNAME]
                ]
            ], 422);
        }
        
        // Check if email already exists for another customer (if provided)
        if ($request->email) {
            $existingEmail = DB::table('TBLCUSTOMER')
                ->where('EMAIL', $request->email)
                ->where('id', '!=', $id)
                ->first(['id', 'CUSTNAME']);
                
            if ($existingEmail) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'email' => ['This email is already used by customer: ' . $existingEmail->CUSTNAME]
                    ]
                ], 422);
            }
        }
        
        // If validation passes, update the customer record
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

