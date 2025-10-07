<?php

namespace App\Console\Commands\CPAR;

use App\Models\CPAR\CustomerComplaint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Revolution\Google\Sheets\Facades\Sheets;
use App\Mail\CPAR\CustomerComplaint as CPARCustomerComplaint;
use App\Models\CPAR\CustomerComplaintUser;
use App\User;
use Carbon\Carbon;

class GetCustomerComplaint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpar:get-customer-complaint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function create_production_date($data)
    {
        // $data['expired_code'] format mm/dd/yyyy
        // If $data['production_distribution'] is Local then production_date is $data['expired_code'] - 8 months else $data['expired_code'] - 12 months
        // $data['production_date'] is null now
        if($data['Product Distribution'] == 'Local') {
            $data['production_date'] = Carbon::createFromFormat('m/d/Y', $data['Expired Code'])->subMonths(8);
        } else {
            $data['production_date'] = Carbon::createFromFormat('m/d/Y', $data['Expired Code'])->subMonths(12);
        }

        // $data['production_date'] format yyyy-mm-dd
        $data['production_date'] = $data['production_date']->format('Y-m-d');

        return $data['production_date'];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = Sheets::getAccessToken();

        $token = [
            'access_token'  => config('google.access_token'),
            'refresh_token' => config('google.refresh_token'),
            // Never expires
            // 'expires_in'    => 3600,
            // 'created'       => 1600000000,
        ];

        $rows = Sheets::setAccessToken($token)->spreadsheet(config('google.sheet_id'))->sheet('Form Responses 1')->get();

        $header = $rows->pull(0);
        $values = Sheets::collection($header, $rows);
        $data = $values->toArray();

        // Info
        $this->info('Found the data ' . count($data));

        foreach($data as $d) {
            $uniqueId = str_replace('/', '', str_replace(':', '', str_replace(' ', '', $d['Timestamp'])));

            // Check if the data with transaction id is alredy exist
            $check_customer_complaint = CustomerComplaint::where('transaction_id', $uniqueId)->first();

            if($check_customer_complaint != null) {
                // Yellow info
                $this->warn('Data with id ' . $uniqueId . ' already exist. Skiped.');
                continue;
            }

            $this->info('New data found with id ' . $uniqueId);

            // Create production_date by local and export
            // Get production_date function
            $d['production_date'] = $this->create_production_date($d);

            $customer_complaint = new CustomerComplaint;
            $customer_complaint->transaction_id = $uniqueId;
            $customer_complaint->timestamp = $d['Timestamp'];
            // Create datetime based on google form timestamp string
            $customer_complaint->timestamp_formated = Carbon::createFromFormat('m/d/Y H:i:s', $d['Timestamp']);
            $customer_complaint->creator_email = $d['Email Address'];
            $customer_complaint->kode_pos = $d['ZIP Code'];
            $customer_complaint->code_category = $d['Code Category'];
            $customer_complaint->created_by = $d['Created By'];
            $customer_complaint->complaint_source = $d['Complaint Source'];
            $customer_complaint->plant = $d['Plant'];
            $customer_complaint->product_distribution = $d['Product Distribution'];
            $customer_complaint->complaint_category = $d['Complaint Categori'];
            $customer_complaint->complaint_sub_category = $d['Complaint Sub Categori'];
            $customer_complaint->product_category = $d['Product Category'];
            $customer_complaint->variant = $d['Varian'];
            $customer_complaint->expired_code = $d['Expired Code'];
            $customer_complaint->production_date = $d['production_date'];
            $customer_complaint->time_code = $d['Time Code'];
            $customer_complaint->machine_code = $d['Machine Code'];
            $customer_complaint->base_information = $d['Base Information'];
            $customer_complaint->name = $d['Name'];
            $customer_complaint->address = $d['Address'];
            $customer_complaint->town = $d['Town'];
            $customer_complaint->district = $d['District'];
            $customer_complaint->province = $d['Province'];
            $customer_complaint->clean_storage = $d['Storage Condition [Clean Storage]'];
            $customer_complaint->seal_is_broken = $d['Storage Condition [Seal is Broken]'];
            $customer_complaint->near_soap_insecticide_detergent = $d['Storage Condition [Near soap / insecticide / detergent]'];
            $customer_complaint->complaint_identify = $d['Complaint Identify (Chronologic)'];
            $customer_complaint->complaint_quantity = $d['Complaint Quantity'];
            $customer_complaint->uom = $d['UoM'];
            $customer_complaint->sample_availability = $d['Sample Availability'];
            $customer_complaint->attachment = $d['Attachment'];
            $customer_complaint->save();

            $customer_complaint->transaction_id = $uniqueId;
            $this->info('Data saved with id ' . $uniqueId);

            // Send email to pas pic when the new data arrive
            $this->info('Sending email to pas pic');

            $PICs = CustomerComplaintUser::where('is_confirmer', 'Y')->get();
            $PICsEmails = $PICs->map(function($item) {
                $user = User::where('username', $item->nik)->first();
                if($user == null) {
                    return '';
                }

                if($user->email == null) {
                    return '';
                }

                return $user->email;
            });

            // Remove the empty email
            $PICsEmails = $PICsEmails->filter(function($item) {
                return $item != '';
            });

            if(count($PICsEmails) == 0) {
                $this->warn('No PIC found. Send email Skiped.');
                continue;
            }

            Mail::mailer(setEmail($PICsEmails->first()))
            ->to($PICsEmails->toArray())
            ->send(new CPARCustomerComplaint($customer_complaint, 'Menunggu Konfirmasi'));

            // $customer_complaint->created_by = $d['Email Address'];
        }

        return 0;
    }
}
