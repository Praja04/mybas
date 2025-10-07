<?php

namespace App\Console\Commands\CPAR;

use App\Mail\CPAR\PraCPAR\WeeklyComplaintSummary;
use App\Models\CPAR\CustomerComplaint;
use App\Models\CPAR\CustomerComplaintUser;
use App\User;
use Carbon\Carbon;
use Google\Service\CloudSearch\Card;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GetWeeklyCustomerComplaint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpar:get-weekly-customer-complaint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get summary of customer complaints for the week';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Start getting the data');

        // Carbon start the week from sunday
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        // Last week start date using Carbon
        $startDate = now()->subWeek()->startOfWeek()->format('Y-m-d');
        // Last week end date using Carbon
        $endDate = now()->subWeek()->endOfWeek()->format('Y-m-d');

        // Get complaint data
        $complaintData = CustomerComplaint::whereBetween('timestamp_formated', [$startDate, $endDate])
        ->orderBy('code_category', 'asc')
        ->get();
        $approvedData = $complaintData->where('status', '2')->count();
        $waitingApproveData = $complaintData->where('status', '!=', '2')->where('status', '!=', '-1')->count();
        $rejectedData = $complaintData->where('status', '-1')->count();

        $periode = Carbon::parse($startDate)->format('d-M-Y') . ' - ' . Carbon::parse($endDate)->format('d-M-Y');

        $marketing_users = CustomerComplaintUser::where('is_marketing', 'Y')
            ->where('is_active', 'Y')
            ->get();
        
        $marketing_emails = $marketing_users->map(function ($item) {
            return $item->email;
        });

        $cc_users = CustomerComplaintUser::where(function($query) {
            $query->where('is_confirmer', 'Y')
                ->orWhere('is_qa_approver', 'Y')
                ->orWhere('is_super_gm', 'Y');
        })->where('is_active', 'Y')
        ->get();

        $cc_emails = $cc_users->map(function ($item) {
            $user = User::where('username', $item->nik)
                ->first();

            if($user != null) {
                if($user->email != null && $user->email != '') {
                    return $user->email;
                }
            }
            return '';
        });

        // dd($marketing_emails->toArray(), $cc_emails->unique()->toArray());

        try {
            // Send the data into mail
            Mail::to($marketing_emails->toArray())
            ->cc($cc_emails->unique()->toArray())
            ->send(new WeeklyComplaintSummary($complaintData, $approvedData, $waitingApproveData, $rejectedData, $periode));
        } catch (\Throwable $th) {
            dd($th);
        }

        // Laravel email send text email
        // Mail::raw('This is a test email', function ($message) use ($marketing_emails, $cc_emails) {
        //     $message->to($marketing_emails->toArray())
        //     ->cc($cc_emails->unique()->toArray())
        //     ->subject('Test email');
        // });

        $this->info('Get data succeeed..');
        return 0;
    }
}
