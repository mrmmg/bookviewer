<?php

namespace App\Filament\Actions;

use App\Models\Document;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class ReadBookAction
{
    public static function handle(Document $document)
    {
        $document->load(['user', 'current_user_track']);

        if(!is_null($document->current_user_track)){
            return self::redirectToPDFJs($document, [
                'last_page' => $document?->current_user_track->metadata['last_page']
            ]);
        }

        return self::redirectToPDFJs($document, []);
    }

    /**
     * @param Document $library
     * @param array $params pdfjs query params
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    private static function redirectToPDFJs(Document $document, array $params)
    {
        $pdfjs_path = url('pdfjs/web/viewer.html');

        $hashed_id = Hashids::encode($document->id);

        $params = array_merge([
            'file' => route('document.get', ['hash' => $hashed_id]),
            'hash' => $hashed_id
        ], $params);

        $redirect_path = Str::finish(url($pdfjs_path), '?') . Arr::query($params);

        return redirect($redirect_path);
    }
}
