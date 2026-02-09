<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $modelLabel = 'Setting';

    protected static ?string $pluralModelLabel = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting Information')
                    ->description('Configure application settings')
                    ->schema([
                        TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Key')
                            ->placeholder('setting_name')
                            ->helperText('Unique identifier for this setting')
                            ->rules(['alpha_dash']),

                        Select::make('type')
                            ->required()
                            ->options([
                                'string' => 'String',
                                'integer' => 'Integer',
                                'boolean' => 'Boolean',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                            ->label('Type')
                            ->helperText('Data type of the setting value')
                            ->native(false)
                            ->reactive(),

                        TextInput::make('group')
                            ->maxLength(255)
                            ->label('Group')
                            ->placeholder('general')
                            ->helperText('Logical grouping for settings')
                            ->datalist([
                                'general',
                                'email',
                                'security',
                                'appearance',
                                'notifications',
                            ]),

                        Textarea::make('value')
                            ->required()
                            ->label('Value')
                            ->placeholder('Enter the setting value...')
                            ->helperText('The actual value of this setting')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->maxLength(1000)
                            ->label('Description')
                            ->placeholder('Describe the purpose of this setting...')
                            ->helperText('Optional description (max 1000 characters)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->label('Key')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Key copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('value')
                    ->searchable()
                    ->label('Value')
                    ->limit(50)
                    ->tooltip(fn (Setting $record): string => $record->value)
                    ->formatStateUsing(function ($state): string {
                        if (strlen($state) > 50) {
                            return Str::limit($state, 50);
                        }
                        return $state;
                    }),

                TextColumn::make('type')
                    ->sortable()
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'primary',
                        'integer' => 'success',
                        'boolean' => 'warning',
                        'json' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('group')
                    ->sortable()
                    ->label('Group')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Uncategorized')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Updated At')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ])
                    ->label('Filter by Type'),

                SelectFilter::make('group')
                    ->options(function () {
                        return Setting::query()
                            ->whereNotNull('group')
                            ->distinct()
                            ->pluck('group', 'group')
                            ->toArray();
                    })
                    ->label('Filter by Group'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key', 'asc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSettings::route('/'),
        ];
    }
}
