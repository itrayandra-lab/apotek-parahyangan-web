<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,webp,pdf,csv,doc,docx,xls,xlsx|max:5120',
        ];
    }
}
