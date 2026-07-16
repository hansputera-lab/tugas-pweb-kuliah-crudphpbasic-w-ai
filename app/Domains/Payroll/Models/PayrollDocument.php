<?php

namespace App\Domains\Payroll\Models;

use App\Domains\Employee\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDocument extends Model
{
    protected $fillable = [
        'payroll_item_id',
        'employee_id',
        'name',
        'file_path',
        'file_type',
        'notes',
        'uploaded_by',
    ];

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('payroll.documents.download', $this);
    }

    public function getFileSizeAttribute(): ?string
    {
        $path = storage_path('app/public/' . $this->file_path);
        if (!file_exists($path)) return null;
        $bytes = filesize($path);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    public function getFileIconAttribute(): string
    {
        $ext = pathinfo($this->file_path, PATHINFO_EXTENSION);
        return match (strtolower($ext)) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'word',
            'xls', 'xlsx' => 'excel',
            'jpg', 'jpeg', 'png', 'gif' => 'image',
            default => 'file',
        };
    }
}
