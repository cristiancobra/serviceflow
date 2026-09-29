<?php

namespace App\Http\Controllers\Concerns;

/**
 * Imagens embutidas como data URI, para o DomPDF não precisar buscar arquivos por URL.
 */
trait ConvertsImagesToBase64
{
    /**
     * Imagem enviada pelo usuário (ex: logo da conta), em storage/.
     * Retorna null se não houver imagem cadastrada ou o arquivo não existir.
     */
    protected function userImageToBase64($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        return $this->fileToBase64(public_path('storage/' . $imagePath));
    }

    /**
     * Imagem do próprio sistema, em public/.
     */
    protected function systemImageToBase64($imagePath)
    {
        return $this->fileToBase64(public_path($imagePath));
    }

    private function fileToBase64($imageCompletePath)
    {
        if (!is_file($imageCompletePath)) {
            return null;
        }

        $type = pathinfo($imageCompletePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imageCompletePath);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
