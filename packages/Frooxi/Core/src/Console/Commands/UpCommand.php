<?php

namespace Frooxi\Core\Console\Commands;

use Frooxi\Core\Models\Channel;
use Illuminate\Foundation\Console\UpCommand as BaseUpCommand;

class UpCommand extends BaseUpCommand
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->upAllChannels();

        parent::handle();
    }

    /**
     * Update all channels.
     *
     * @return mixed
     */
    protected function upAllChannels()
    {
        $this->components->info('Activating all channels.');

        return Channel::query()->update(['is_maintenance_on' => 0]);
    }
}
