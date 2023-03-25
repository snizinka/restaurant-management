<?php

namespace App\Services\GeneralOrder;

use App\Models\GeneralOrder;
use Illuminate\Support\Facades\Facade;
/**
 * @method static GeneralOrder create()
 * @see GeneralOrderService
 */
class GeneralOrderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GeneralOrderService::class;
    }
}
