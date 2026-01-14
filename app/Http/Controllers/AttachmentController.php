<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachmentUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(AttachmentUploadRequest $request): JsonResponse
    {
        $file = $request->file('attachment');
        $path = $file->store('attachments', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'name' => $file->getClientOriginalName(),
        ], 201);
    }
}
