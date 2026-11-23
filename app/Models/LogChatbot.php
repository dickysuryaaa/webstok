<?php

namespace App\Models;

use Database\Factories\LogChatbotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogChatbot extends Model
{
    /** @use HasFactory<LogChatbotFactory> */
    use HasFactory;

    protected $table = 'log_chatbot';

    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'produk_id', 'pertanyaan', 'jawaban'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
