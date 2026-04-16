<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Upload de imagens para o editor de texto
     */
    public function uploadEditorImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Gera nome único para evitar conflitos
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Salva na pasta public/storage/img/editor
            $path = $image->storeAs('img/editor', $filename, 'public');
            
            // Retorna a URL completa da imagem
            $url = asset('storage/' . $path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhuma imagem enviada'
        ], 400);
    }
}
