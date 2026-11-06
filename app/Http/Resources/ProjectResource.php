<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'experience_level' => $this->experience_level,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'proposal_deadline' => $this->proposal_deadline?->format('Y-m-d H:i:s'),
            'budget_min' => $this->budget_min ? (float) $this->budget_min : null,
            'budget_max' => $this->budget_max ? (float) $this->budget_max : null,
            'is_featured' => $this->is_featured,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'color' => $this->category->color,
            ],
            'skills' => $this->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                ];
            }),
            'proposals_count' => $this->proposals->count(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
