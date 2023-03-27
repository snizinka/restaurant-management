<?php

namespace App\Services\Driver;

use App\Models\Driver;
use Illuminate\Support\Facades\Facade;
/**
 * @method static Driver create($data)
 * @method static update($id, $data)
 * @method static delete($id)
 * @method static getDriver($id)
 * @see DriverService
 */
class DriverFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DriverService::class;
    }
}
