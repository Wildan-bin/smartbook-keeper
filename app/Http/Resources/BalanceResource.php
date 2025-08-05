<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'current_amount' => $this->current_amount,
            'latest_transaction' => $this->transactions->first() ? [
                'date' => $this->transactions->first()->date,
                'amount' => $this->transactions->first()->amount,
                'type' => $this->transactions->first()->type,
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
