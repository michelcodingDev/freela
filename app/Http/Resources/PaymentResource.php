<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'platform_fee' => (float) $this->platform_fee,
            'gateway_fee' => (float) $this->gateway_fee,
            'net_amount' => (float) $this->net_amount,
            'method' => $this->method,
            'status' => $this->status,
            'payment_intent_id' => $this->payment_intent_id,
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'released_at' => $this->released_at?->format('Y-m-d H:i:s'),
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ],
            'freelancer' => [
                'id' => $this->freelancer->id,
                'name' => $this->freelancer->name,
            ],
            'project' => [
                'id' => $this->project->id,
                'title' => $this->project->title,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
