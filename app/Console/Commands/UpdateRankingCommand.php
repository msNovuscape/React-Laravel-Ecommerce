<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\User;

class UpdateRankingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ambassadors = User::ambassador()->get();

        $bar = $this->output->createProgressBar($ambassadors->count());
        $bar->start();

        $ambassadors->each(function(User $user){
            Redis::zadd('rankings', $user->revenue, $user->name);
            $bar->advance();
        });

        $bar->finish();
     
    }
}
