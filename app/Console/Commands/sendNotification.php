<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\SendMail;
use App\Http\Controllers\Traits\Common;
use Illuminate\Support\Facades\DB;
use App\User;
use JWTAuth;
use Mail;
use App\Mail\RegistrationMail;

class sendNotification extends Command
{
	use SendMail;
  	use Common;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plannotification:default';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
	//$phone = '9812045298';
	//$userData = User::where('phone',$phone)->first();
	$userData = User::get();
	foreach($userData as $value){
		$diviceIds = [$value->device_token];
		if(isset($diviceIds)){
                    $suscription_title = "Plan Subscription";
                    $suscription_message = "Your Plan is Expiring within 5 Days";
		    //$toMail = 'nitesh182185048@gmail.com';
		    $toMail = $value->email;
                    $this->sendNotification($diviceIds,'',$suscription_title,$suscription_message);
		    Mail::raw('Your Plan is Expiring within 5 Days!', function ($message) use ($toMail) {
  			$message->to($toMail)->subject('Plan Subscription');
			});
            }
	}
	
		
    }
}
