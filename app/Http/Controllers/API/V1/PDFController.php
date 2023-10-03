<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\Library;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PDFController extends Controller
{
    public function getUser(Request $request)
    {
        $status = false;

        if(Auth::check()){
            $status = true;
        }

        return response()->json([
            'message' => $status
        ]);
    }

    public function updateLastPageData(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'page_number' => 'required|numeric',
            'hash' => 'required|string|min:8'
        ]);

        if($validate->fails()){
            return response()->json([
                'message' => false,
                'bag' => $validate->messages()
            ]);
        }

        $hashids = Hashids::decode($request->hash);

        if(empty($hashids)){
            return response()->json([
                'message' => false,
                'bag' => [
                    'Bad hash sent'
                ]
            ]);
        }

        $document = Document::query()
            ->with(['user', 'current_user_track'])
            ->where('id', $hashids[0])
            ->first();

        if(is_null($document?->current_user_track)){
            $data = [
                'user_id' => Auth::id(),
                'document_id' => $document?->id,

                'metadata' => [
                    'last_page' => $request->page_number,
                    'last_reading_time' => time()
                ]
            ];

            DocumentTrack::query()->create($data);
            goto response;
        } else {
            $metadata = $document?->current_user_track->metadata;

            $metadata['last_page'] = $request->page_number;
            $metadata['last_reading_time'] = time();

            $document->current_user_track->metadata = $metadata;
            $document->current_user_track->save();
        }

        response:
            return response()->json([
                'message' => true,
                'doc' => $document
            ]);
    }
}
