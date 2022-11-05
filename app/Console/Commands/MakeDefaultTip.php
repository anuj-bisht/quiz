<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class MakeDefaultTip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maketip:default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will be used to make default tip';

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
        DB::table('districts')->where('id',1)->update(['district_name'=>'Cron',"status"=>'N']);
        // $record = DB::table('tips')->inRandomOrder()->first();
        // if(isset($record->id)){
        //     DB::table('tips')->where('id',$record->id)->update(["default",'1']);
        // }
                    
    }
}
