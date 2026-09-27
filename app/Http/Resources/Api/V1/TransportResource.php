<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bus records include a driver's name and phone number, so the field list is
 * explicit rather than whatever the table happens to contain.
 *
 * @mixin Bus
 */
class TransportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bus_no' => $this->bus_no,
            'driver_name' => $this->driver_name,
            'driver_phone' => $this->driver_phone,
            'capacity' => $this->capacity,
            'route_name' => $this->route_name,
        ];
    }
}
