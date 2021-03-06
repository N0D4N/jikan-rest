<?php

namespace App\Console\Commands;

use App\Http\Middleware\Blacklist;
use Illuminate\Console\Command;

class BlacklistAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:add {ip} {--reload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an IP to the blacklist';

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
        $ip = $this->argument('ip');
        $reload = $this->option('reload');

        $blacklist = json_decode(file_get_contents(BLACKLIST_PATH), true);

        if (\in_array($ip, $blacklist)) {
            $this->error("IP is already blacklisted");
            return;
        }

        $blacklist[] = $ip;
        file_put_contents(BLACKLIST_PATH, json_encode($blacklist));
        $this->info("Blacklisted IP: {$ip}");

        if ($reload) {
            Blacklist::reloadList();
            $this->info("Blacklist reloaded into Redis");
        }
    }
}
