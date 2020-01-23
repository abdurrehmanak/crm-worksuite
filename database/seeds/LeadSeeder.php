<?php

use Illuminate\Database\Seeder;


class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('lead_sources')->delete();
        \DB::table('lead_status')->delete();
        \DB::table('leads')->delete();
        // Lead Source start
        $sources = new \App\LeadSource();
        $sources->type = 'Social Media';
        $sources->save();

        $sources = new \App\LeadSource();
        $sources->type = 'Google';
        $sources->save();

        $sources = new \App\LeadSource();
        $sources->type = 'other';
        $sources->save();
        // Lead Source end

        // Lead Status start
        $sources = new \App\LeadStatus();
        $sources->type = 'Pending';
        $sources->save();

        $sources = new \App\LeadStatus();
        $sources->type = 'Overview';
        $sources->save();

        $sources = new \App\LeadStatus();
        $sources->type = 'Confirmed';
        $sources->save();
        // Lead Status end

        $lead = new \App\Lead();
        $lead->company_name = 'Test Lead';
        $lead->website = 'www.testing.com';
        $lead->address = 'www.testing.com';
        $lead->client_name = 'Test client';
        $lead->client_email = 'testing@test.com';
        $lead->mobile = '123456789';
        $lead->note = 'Quas consectetur, tempor incidunt, aliquid voluptatem, velit mollit et illum, adipisicing ea officia aliquam placeat';
        $lead->save();

    }
}
