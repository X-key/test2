<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DemoTest
 * @package App\Models
 */
final class DemoTest extends Model
{
    use HasFactory;

    protected $table = 'demo_test';
    protected $fillable = [
        'ref',
        'name',
        'description',
        'status',
        'is_active',
    ];

    /**
     * @param array $refs
     * @param bool $isActive
     * @return array
     */
    public static function updateRecords(array $refs, bool $isActive = true): array
    {
        $existingRefs = self::whereIn('ref', $refs)
            ->pluck('ref')
            ->toArray();

        $notActivatedRefs = self::whereIn('ref', $existingRefs)
            ->where('is_active', !$isActive)
            ->pluck('ref')
            ->toArray();

        self::whereIn('ref', $notActivatedRefs)
            ->update(['is_active' => $isActive]);

        $notFoundRefs = array_diff($refs, $existingRefs);

        $messagePrefix = $isActive ? 'activated' : 'deactivated';

        return [
            $messagePrefix.'_refs_count' => count($notActivatedRefs),
            $messagePrefix.'_refs_list' => $notActivatedRefs,
            'not_found_refs_count' => count($notFoundRefs),
            'not_found_refs_list' => $notFoundRefs,
            'already_' . $messagePrefix . '_refs_count' => ($existingRefs ? count($existingRefs) - count($notActivatedRefs) : 0),
        ];
    }
}
