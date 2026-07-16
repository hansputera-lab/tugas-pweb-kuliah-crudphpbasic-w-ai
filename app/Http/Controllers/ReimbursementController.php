<?php

namespace App\Http\Controllers;

use App\Domains\Reimbursement\Models\ExpenseCategory;
use App\Domains\Reimbursement\Models\ReimbursementClaim;
use App\Domains\Reimbursement\Services\ReimbursementService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReimbursementController extends Controller
{
    public function __construct(
        protected ReimbursementService $reimbursementService
    ) {}

    public function index()
    {
        $claims = $this->reimbursementService->getAll();

        return view('reimbursement.index', compact('claims'));
    }

    public function show(int $id)
    {
        $claim = $this->reimbursementService->getById($id);

        if (!$claim) {
            abort(404);
        }

        $user = Auth::user();
        if ($user->role === 'employee' && $claim->employee_id !== $user->employee?->id) {
            abort(403);
        }

        return view('reimbursement.show', compact('claim'));
    }

    public function approve(Request $request, int $id)
    {
        $claim = $this->reimbursementService->getById($id);

        if (!$claim) {
            abort(404);
        }

        try {
            $this->reimbursementService->approve($claim, Auth::user(), $request->input('notes'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Claim approved at level ' . $claim->current_approval_level . '.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        $claim = $this->reimbursementService->getById($id);

        if (!$claim) {
            abort(404);
        }

        try {
            $this->reimbursementService->reject($claim, Auth::user(), $request->input('notes'), $request->input('rejected_reason'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Claim rejected.');
    }

    // Employee self-service
    public function create()
    {
        $categories = $this->reimbursementService->getCategories();

        return view('reimbursement.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date|before_or_equal:today',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $category = $this->reimbursementService->getCategory($validated['expense_category_id']);
        if ($category && $category->requires_receipt && !$request->hasFile('receipt')) {
            return back()->withInput()->with('error', 'Receipt is required for this category.');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $filename = 'rc_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/reimbursements'), $filename);
            $receiptPath = $filename;
        }

        $this->reimbursementService->submitClaim($employee, array_merge($validated, [
            'receipt_path' => $receiptPath,
        ]));

        return redirect()->route('my.claims')
            ->with('success', 'Reimbursement claim submitted.');
    }

    public function myClaims()
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked.');
        }

        $claims = $this->reimbursementService->getByEmployee($employee->id);

        return view('reimbursement.my.claims', compact('claims', 'employee'));
    }

    // Category management (HR)
    public function categories()
    {
        $categories = $this->reimbursementService->getAllCategories();

        return view('reimbursement.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'requires_receipt' => 'nullable|boolean',
            'approval_levels' => 'required|integer|min:1|max:3',
        ]);

        $validated['requires_receipt'] = $request->has('requires_receipt');
        $this->reimbursementService->createCategory($validated);

        return redirect()->route('reimbursement.categories')
            ->with('success', 'Expense category added.');
    }

    public function updateCategory(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'requires_receipt' => 'nullable|boolean',
            'approval_levels' => 'required|integer|min:1|max:3',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['requires_receipt'] = $request->has('requires_receipt');
        $validated['is_active'] = $request->has('is_active');
        $this->reimbursementService->updateCategory($id, $validated);

        return redirect()->route('reimbursement.categories')
            ->with('success', 'Expense category updated.');
    }

    public function destroyCategory(int $id)
    {
        $this->reimbursementService->deleteCategory($id);

        return redirect()->route('reimbursement.categories')
            ->with('success', 'Expense category deleted.');
    }
}
