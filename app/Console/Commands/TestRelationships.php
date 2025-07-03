<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\Customer;

class TestRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:relationships';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the site-customer relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Site-Customer Relationships');
        $this->info('=====================================');

        // Test Site 30 (MNC Cebu - main)
        $site30 = Site::find(30);
        if ($site30) {
            $this->info("Site 30: " . $site30->name);
            $this->info("Customer count: " . $site30->customers()->count());
            $sampleCustomer = $site30->customers()->first();
            if ($sampleCustomer) {
                $this->info("Sample customer: " . $sampleCustomer->CUSTNAME);
            }
            $this->info('');
        }

        // Test Site 31 (MNC Bohol)
        $site31 = Site::find(31);
        if ($site31) {
            $this->info("Site 31: " . $site31->name);
            $this->info("Customer count: " . $site31->customers()->count());
            $sampleCustomer = $site31->customers()->first();
            if ($sampleCustomer) {
                $this->info("Sample customer: " . $sampleCustomer->CUSTNAME);
            }
            $this->info('');
        }

        // Test customer relationship back to site
        $customer = Customer::first();
        if ($customer) {
            $this->info("Customer: " . $customer->CUSTNAME);
            $this->info("Belongs to site: " . $customer->site->name);
            $this->info("Site SBU: " . $customer->site->sbu->name);
        }

        $this->info('');
        $this->info('Total customers: ' . Customer::count());
        $this->info('Site 30 customers: ' . Customer::where('site_id', 30)->count());
        $this->info('Site 31 customers: ' . Customer::where('site_id', 31)->count());
        
        return 0;
    }
}
