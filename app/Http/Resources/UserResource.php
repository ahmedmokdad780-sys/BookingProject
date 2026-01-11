<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'account_type'   => $this->account_type,
            // التعديل هنا: استخدم اسم العمود الفعلي مع asset
            'personal_image' => $this->personal_image ? asset('storage/' . $this->personal_image) : null,
        ];
    }
}
