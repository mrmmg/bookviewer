<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ReadBookAction;
use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use App\Models\Library;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(512)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('pdf_file')
                    ->required()
                    ->disk('public')
                    ->directory('books')
                    ->preserveFilenames()
                    ->openable()
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpanFull()
                    ->hiddenOn(Pages\EditDocument::class),

                Forms\Components\Toggle::make('is_public')
                    ->label('Is public?')
                    ->hint('Private means file is accessible to you and selected users!')
                    ->hintColor('primary')
                    ->inline(false)
                    ->reactive(),

                Forms\Components\Select::make('share_with')
                    ->label('Share With')
                    ->multiple()
                    ->options(
                        fn() => User::query()
                            ->where('id', '!=', Auth::id())->pluck('name', 'id')->toArray()
                    )
                    ->searchable()
                    ->hidden(fn(callable $get) => $get('is_public') === true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uploader'),

                Tables\Columns\TextColumn::make('is_public')
                    ->label('Is public?')
                    ->formatStateUsing(fn(Document $document) => $document->is_public ? "Yes" : "No"),

                Tables\Columns\TextColumn::make('current_user_track.metadata')
                    ->label('Last Page')
                    ->formatStateUsing(function (Document $document) {
                        if (is_null($document->current_user_track)) {
                            return 0;
                        }

                        return $document?->current_user_track->metadata['last_page'];
                    }),

                Tables\Columns\TextColumn::make('current_user_track')
                    ->label('Last Reading Time')
                    ->formatStateUsing(function (Document $document){
                        $last_time = $document?->current_user_track->metadata['last_reading_time'];

                        if($last_time === 0) {
                            return "Not Started";
                        }

                        return Carbon::parse($last_time)
                            ->timezone('Asia/Tehran')
                            ->format('Y/m/d H:i:s');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('read')
                    ->icon('heroicon-s-magnifying-glass-plus')
                    ->link()
                    ->action(fn(Document $document) => ReadBookAction::handle($document))
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
