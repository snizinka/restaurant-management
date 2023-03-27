<?php

namespace App\Services\GeneralOrder;

use App\Models\GeneralOrder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
/**
 * @method static GeneralOrder create()
 * @method static delete($id)
 * @method static getDeneralOrder($id)
 * @method static assignDriverToOrder($generalOrder_id, $driver_id)
 * @method static GeneralOrder placeOrder(GeneralOrder $generalOrder, $data)
 * @see GeneralOrderService
 */
class GeneralOrderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GeneralOrderService::class;
    }
}
