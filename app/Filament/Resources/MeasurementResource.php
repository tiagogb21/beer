<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeasurementResource\Pages;
use App\Filament\Resources\MeasurementResource\RelationManagers;
use App\Models\Measurement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeasurementResource extends Resource
{
    protected static ?string $model = Measurement::class;

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Medidas';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('unit'),
                                Forms\Components\TextInput::make('value')
                                    ->type('number')
                            ])
                    ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMeasurements::route('/'),
            'create' => Pages\CreateMeasurement::route('/create'),
            'edit' => Pages\EditMeasurement::route('/{record}/edit'),
        ];
    }
}
