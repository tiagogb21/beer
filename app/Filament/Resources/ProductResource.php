<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true),
                            ]),

                            Forms\Components\MarkdownEditor::make('description')
                                ->columnSpan('full')
                                ->label('Descrição'),
                        ])
                        ->columns(2),

                    Forms\Components\Section::make('Imagens')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('media')
                                ->collection('product-images')
                                ->multiple()
                                ->maxFiles(5)
                                ->hiddenLabel(),
                        ])
                        ->collapsible(),

                    Forms\Components\Section::make('Preço')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->label('Preço')
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                ->required(),
                        ])
                        ->columns(2),
                    Forms\Components\Section::make('Inventário')
                        ->schema([
                            Forms\Components\TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->rules(['integer', 'min:0'])
                                ->required(),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('is_visible')
                                ->label('Visible')
                                ->helperText('Este produto ficará oculto em todos os canais de vendas.')
                                ->default(true),
                        ]),

                    Forms\Components\Section::make('Associações')
                        ->schema([
                            Forms\Components\Select::make('categories')
                                ->label('categorias')
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->required(),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ])
        ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label('Imagem')
                    ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibilidade')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name'),
                        TextConstraint::make('slug'),
                        TextConstraint::make('sku')
                            ->label('SKU (Stock Keeping Unit)'),
                        TextConstraint::make('barcode')
                            ->label('Barcode (ISBN, UPC, GTIN, etc.)'),
                        TextConstraint::make('description'),
                        NumberConstraint::make('old_price')
                            ->label('Compare at price')
                            ->icon('heroicon-m-currency-dollar'),
                        NumberConstraint::make('price')
                            ->icon('heroicon-m-currency-dollar'),
                        NumberConstraint::make('cost')
                            ->label('Cost per item')
                            ->icon('heroicon-m-currency-dollar'),
                        NumberConstraint::make('qty')
                            ->label('Quantity'),
                        NumberConstraint::make('security_stock')
                            ->label('Estoque seguro'),
                        BooleanConstraint::make('is_visible')
                            ->label('Visibilidade'),
                        BooleanConstraint::make('featured'),
                        BooleanConstraint::make('backorder'),
                        BooleanConstraint::make('requires_shipping')
                            ->icon('heroicon-m-truck'),
                        DateConstraint::make('published_at')
                            ->label('Data de publicação'),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->deferFilters()
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
