<?php

namespace System\Providers;

use System\Provider;

use System\Database;

/**
 * Register Database
 *
 * @author Ceddy
 */
class DatabaseProvider extends Provider
{
    public function register()
    {
        $this->oContainer->set('singleton', 'Database', function ($oContainer) {
            return new Database(config('database'));
        });
    }
}
