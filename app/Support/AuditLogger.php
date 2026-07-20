<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(
        string $event,
        ?Model $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
        ?Request $request = null
    ): void {
        $request ??= request();

        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'metadata' => $metadata ?: null,
        ]);
    }
}
