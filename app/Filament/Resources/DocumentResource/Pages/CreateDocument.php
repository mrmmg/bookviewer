<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\Library;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use JsonException;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    private array $document_data;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->saveAdditionalData($data);

        $file_content = Storage::disk('public')->get($data['pdf_file']);

        $data['checksum'] = md5($file_content);
        $data['store_path'] = $data['pdf_file'];
        $data['created_by'] = Auth::id();

        $is_exists = Document::query()
            ->where('checksum', $data['checksum'])
            ->exists();

        if($is_exists){
            Notification::make()
                ->title("This book is already exists!")
                ->danger()
                ->send();

            $this->halt();
        }

        //save memory
        unset($data['pdf_file'], $file_content);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return parent::getRedirectUrl();
    }

    private function saveAdditionalData(array $data)
    {
        $this->document_data['share_with'] = $data['share_with'] ?? [];

        unset($data['share_with']);
    }

    /**
     * @throws JsonException
     */
    protected function afterCreate(): void
    {
        $is_shared = !empty($this->document_data['share_with']);

        DocumentTrack::query()->create([
            'user_id' => Auth::id(),
            'document_id' =>  $this->record->id,
            'is_shared' => $is_shared,
            'shared_by' => $is_shared ? Auth::id() : NULL,
            'metadata' => json_decode($this->generateSharedMetadata(), true, 512, JSON_THROW_ON_ERROR)
        ]);

       if(!$is_shared) {
           return;
       }

       $inserts = [];

       foreach ($this->document_data['share_with'] as $shared_user_id){
            $inserts[] = [
                'user_id' => (int)$shared_user_id,
                'document_id' =>  $this->record->id,
                'is_shared' => true,
                'shared_by' => $this->record->created_by,
                'metadata' => $this->generateSharedMetadata()
            ];
       }

       DocumentTrack::query()
           ->insert($inserts);
    }

    /**
     * @throws JsonException
     */
    private function generateSharedMetadata(): string
    {
        return json_encode([
            'last_page' => 0,
            'last_reading_time' => 0
        ], JSON_THROW_ON_ERROR);
    }
}
