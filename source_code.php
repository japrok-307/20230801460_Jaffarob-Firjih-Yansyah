<?php

// Migration: database/migrations/2025_05_21_000000_create_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Sensitive data encrypted
            $table->string('card_holder_name');
            $table->string('card_number')->nullable();
            $table->string('expiry_date');
            $table->string('cvv')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

// Model: app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_holder_name',
        'card_number',
        'expiry_date',
        'cvv',
        'amount',
    ];

    // Encrypt card_number and cvv automatically
    protected $casts = [
        'card_number' => 'encrypted',
        'cvv'         => 'encrypted',
    ];

    // Hide sensitive fields on serialization
    protected $hidden = [
        'card_number',
        'cvv',
    ];

    public function user()
    {
        return \$this->belongsTo(User::class);
    }
}

// Policy: app/Policies/PaymentPolicy.php
namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function view(User \$user, Payment \$payment): bool
    {
        return \$user->id === \$payment->user_id || \$user->hasRole('admin');
    }

    public function update(User \$user, Payment \$payment): bool
    {
        return \$user->id === \$payment->user_id;
    }

    public function delete(User \$user, Payment \$payment): bool
    {
        return \$user->hasRole('admin');
    }
}

// Register policy: app/Providers/AuthServiceProvider.php
protected \$policies = [
    Payment::class => PaymentPolicy::class,
];

// Filament Resource: app/Filament/Resources/PaymentResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;

class PaymentResource extends Resource
{
    protected static ?string \$model = Payment::class;
    protected static ?string \$navigationIcon = 'heroicon-o-credit-card';
    protected static ?string \$navigationLabel = 'Payments';
    protected static ?string \$recordTitleAttribute = 'card_holder_name';

    public static function form(Form \$form): Form
    {
        return \$form
            ->schema([
                Forms\Components\TextInput::make('card_holder_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('card_number')
                    ->label('Card Number')
                    ->required()
                    ->mask(fn (Forms\Components\TextInput\Mask \$mask) => \$mask->pattern('#### #### #### ####'))
                    ->dehydrateStateUsing(fn (\$state) => str_replace(' ', '', \$state)),
                Forms\Components\TextInput::make('expiry_date')
                    ->label('Expiry (MM/YY)')
                    ->required()
                    ->mask('00/00'),
                Forms\Components\TextInput::make('cvv')
                    ->label('CVV')
                    ->required()
                    ->maxLength(4),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('USD '),
            ]);
    }

    public static function table(Table \$table): Table
    {
        return \$table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('card_holder_name'),
                Tables\Columns\TextColumn::make('card_number')
                    ->label('Card Number')
                    ->formatStateUsing(fn (\$record) => '**** **** **** ' . substr(\$record->card_number, -4)),
                Tables\Columns\TextColumn::make('amount')->money('usd'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('recent')
                    ->query(fn (\$query) => \$query->where('created_at', '>=', now()->subWeek())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}