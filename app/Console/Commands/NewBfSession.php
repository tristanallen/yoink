<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BetFairApi;
use App\Models\BetFairUser;

class NewBfSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bf:newSession';

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
     * @return mixed
     */
    public function handle()
    {
        $user = BetFairUser::find(1);

        $bf = new BetFairApi();

        $decoded = $bf->newSession($user);

        $user->betfair_session = $decoded->token;
        $user->save();

        $this->info('session id saved');
    }
}
