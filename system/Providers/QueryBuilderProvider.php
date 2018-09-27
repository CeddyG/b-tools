<?php

namespace System\Providers;

use System\Provider;

use System\QueryBuilder;

/**
 * Register QueryBuilder.
 *
 * @author Ceddy
 */
class QueryBuilderProvider extends Provider
{
    public function register()
    {
        $this->oContainer->set('factory', 'QueryBuilder', function ($oContainer) {
            return new QueryBuilder($oContainer->get('Database'));
        });
    }
}
