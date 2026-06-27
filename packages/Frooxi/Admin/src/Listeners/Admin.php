<?php

namespace Frooxi\Admin\Listeners;

class Admin
{
    /**
     * Send mail on updating password.
     *
     * @param  \Frooxi\User\Models\Admin  $admin
     * @return void
     */
    public function afterPasswordUpdated($admin) {}
}
