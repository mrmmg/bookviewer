<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function getDocument($hash, Request $request)
    {
        $document_id = app('hashids')->decode($hash)[0];

        $document = Document::with('tracks')
            ->where('id', $document_id)
            ->first();

        if(
            $document?->is_public ||
            (!$document?->is_public && $document?->created_by === Auth::id())
        ) {
            return $this->makeDownloadDocument($document?->store_path);
        }

        foreach ($document?->tracks as $track){
            if($track->is_shared && $track->user_id === Auth::id()){
                return $this->makeDownloadDocument($document?->store_path);
            }
        }

        return abort(403);
    }

    private function makeDownloadDocument($store_path)
    {
        return Storage::disk('public')->download($store_path);
    }
}
