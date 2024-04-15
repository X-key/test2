<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class DemoTestInquiry extends Model
{
    use HasFactory;

    protected $table = 'demo_test_inquiry';
    protected $fillable = [
        'payload',
        'items_total_count',
        'items_processed_count',
        'items_failed_count',
    ];
    protected $casts = [
        'payload' => 'json',
    ];
}
