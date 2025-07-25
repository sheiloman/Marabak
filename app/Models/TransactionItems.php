<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItems extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transactions()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function foods()
    {
        return $this->belongsTo(Foods::class);
    }
}
