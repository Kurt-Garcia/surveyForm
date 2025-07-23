<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Return a list of customer names matching the search term for autocomplete.
     */
    public function autocomplete(Request $request)
    {
        $term = $request->get('term');
        $siteIds = $request->get('site_ids'); // Array of site IDs from the user
        
        $query = DB::table('TBLCUSTOMER')
            ->where(function($q) use ($term) {
                $q->where('CUSTNAME', 'like', '%' . $term . '%')
                  ->orWhere('CUSTCODE', 'like', '%' . $term . '%');
            });
            
        // Filter by site_ids if provided
        if (!empty($siteIds) && is_array($siteIds)) {
            $query->whereIn('site_id', $siteIds);
        }
            
        $results = $query->limit(20)
            ->get(['CUSTCODE as custcode', 'CUSTNAME as label', 'CUSTNAME as value', 'CUSTTYPE as custtype', 'site_id']);
            
        return response()->json($results);
    }
    
    /**
     * Look up customer information by customer code.
     */
    public function lookupByCode(Request $request)
    {
        $code = $request->get('code');
        $siteIds = $request->get('site_ids'); // Array of site IDs from the user
        
        $query = DB::table('TBLCUSTOMER')
            ->where('CUSTCODE', $code);
            
        // Filter by site_ids if provided
        if (!empty($siteIds) && is_array($siteIds)) {
            $query->whereIn('site_id', $siteIds);
        }
            
        $customer = $query->first(['CUSTCODE as custcode', 'CUSTNAME as custname', 'CUSTTYPE as custtype', 'site_id']);
            
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
        // Log the request data for debugging
        Log::info('Customer update request', [
            'id' => $id,
            'phone' => $request->phone,
            'email' => $request->email
        ]);
        
        // Validate the request data
        $validator = Validator::make([
            'phone' => $request->phone,
            'email' => $request->email,
        ], [
            'phone' => ['required', 'string', function ($attribute, $value, $fail) {
                // Check for Philippine mobile number format
                if (!preg_match('/^(\+639\d{9}|09\d{9})$/', $value)) {
                    $fail('Contact number must be in Philippine mobile format: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (13 digits).');
                }
            }],
            'email' => 'nullable|email|max:255',
        ]);
        
        if ($validator->fails()) {
            Log::warning('Customer update validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if phone number already exists for another customer
        $existingPhone = DB::table('TBLCUSTOMER')
            ->where('CONTACTCELLNUMBER', $request->phone)
            ->where('id', '!=', $id)
            ->first(['id', 'CUSTNAME']);
            
        if ($existingPhone) {
            Log::warning('Phone number already exists', ['customer' => $existingPhone->CUSTNAME]);
            return response()->json([
                'success' => false,
                'errors' => [
                    'phone' => ['This phone number is already used by customer: ' . $existingPhone->CUSTNAME]
                ]
            ], 422);
        }
        
        // Check if email already exists for another customer (only if email is not empty)
        if (!empty(trim($request->email))) {
            $existingEmail = DB::table('TBLCUSTOMER')
                ->whereRaw('LOWER(TRIM(EMAIL)) = LOWER(TRIM(?))', [$request->email])
                ->where('id', '!=', $id)
                ->whereNotNull('EMAIL')
                ->where('EMAIL', '!=', '')
                ->first(['id', 'CUSTNAME']);
                
            if ($existingEmail) {
                Log::warning('Email already exists', ['customer' => $existingEmail->CUSTNAME]);
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'email' => ['This email is already used by customer: ' . $existingEmail->CUSTNAME]
                    ]
                ], 422);
            }
        }
        
        // If validation passes, update the customer record
        try {
            // Get customer name before update for activity logging
            $customer = DB::table('TBLCUSTOMER')
                ->where('id', $id)
                ->first(['CUSTNAME']);
            
            DB::table('TBLCUSTOMER')
                ->where('id', $id)
                ->update([
                    'CONTACTCELLNUMBER' => $request->phone,
                    'EMAIL' => $request->email,
                    'updated_at' => now()
                ]);
            
            // Log activity
            if ($customer) {
                activity()
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'customer_id' => $id,
                        'customer_name' => $customer->CUSTNAME,
                        'updated_fields' => ['phone', 'email']
                    ])
                    ->event('updated')
                    ->log($customer->CUSTNAME . "'s info has been updated in customers list");
            }
            
            Log::info('Customer updated successfully', ['id' => $id]);
            return response()->json([
                'success' => true,
                'message' => 'Customer contact information updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating customer', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'errors' => ['general' => ['An error occurred while updating the customer.']]
            ], 500);
        }
    }

    /**
     * Check if an email is available (not used by another customer)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmailAvailability(Request $request)
    {
        $email = trim($request->query('email'));
        $customerId = $request->query('customer_id'); // Current customer ID being edited
        
        if (!$email) {
            return response()->json([
                'exists' => false,
                'message' => 'Email is required'
            ]);
        }
        
        // Check if email exists in TBLCUSTOMER for another customer (case-insensitive and whitespace-trimmed)
        $query = DB::table('TBLCUSTOMER')
            ->whereRaw('LOWER(TRIM(EMAIL)) = LOWER(TRIM(?))', [$email])
            ->whereNotNull('EMAIL')
            ->where('EMAIL', '!=', '');
            
        // If editing existing customer, exclude their current record
        if ($customerId) {
            $query->where('id', '!=', $customerId);
        }
        
        $existingCustomer = $query->first(['id', 'CUSTNAME']);
        
        if ($existingCustomer) {
            return response()->json([
                'exists' => true,
                'message' => 'This email is already used by customer: ' . $existingCustomer->CUSTNAME
            ]);
        }
        
        return response()->json([
            'exists' => false,
            'message' => 'Email is available'
        ]);
    }
}

