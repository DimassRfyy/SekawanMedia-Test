<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('vehicle_id')
                    ->required()
                    ->relationship('vehicle', 'name'),
                Forms\Components\Select::make('driver_id')
                    ->required()
                    ->relationship('driver', 'name'),
                Forms\Components\Select::make('region_id')
                    ->required()
                    ->relationship('region', 'name'),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name', function ($query) {
                        return $query->where('role', 'approver');
                    }),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->default('waiting')
                    ->options([
                        'waiting' => 'Waiting Confirmation',
                        'process' => 'Processing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('region.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Approver')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('to_process')
                    ->label('Set to Process')
                    ->visible(fn ($record) => Auth::user()->role === 'approver' && $record->status === 'waiting')
                    ->requiresConfirmation()
                    ->action(function ($record, $data, $action) {
                        $record->status = 'process';
                        $record->save();
                        Notification::make()
                            ->title('Booking set to process')
                            ->success()
                            ->body('The booking status has been updated to "Processing".')
                            ->send();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path'),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->visible(fn ($record) => Auth::user()->role === 'approver' && $record->status === 'process')
                    ->requiresConfirmation()
                    ->action(function ($record, $data, $action) {
                        $record->status = 'approved';
                        $record->save();
                        Notification::make()
                            ->title('Booking approved')
                            ->success()
                            ->body('The booking has been approved.')
                            ->send();
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->visible(fn ($record) => Auth::user()->role === 'approver' && $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record, $data, $action) {
                        $record->status = 'rejected';
                        $record->save();
                        Notification::make()
                            ->title('Booking rejected')
                            ->danger()
                            ->body('The booking has been rejected.')
                            ->send();
                    })
                    ->color('danger')
                    ->icon('heroicon-o-x-circle'),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()->role === 'admin'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBookings::route('/'),
        ];
    }
}
