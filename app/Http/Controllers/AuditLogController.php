<?php

namespace App\Http\Controllers;

use App\Domains\ActivityLog\Services\ActivityLogService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $logs = $this->activityLogService->getRecent(100);

        return view('audit-log.index', compact('logs'));
    }
}
