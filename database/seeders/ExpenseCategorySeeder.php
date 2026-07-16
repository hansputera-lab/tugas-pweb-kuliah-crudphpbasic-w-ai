<?php

namespace Database\Seeders;

use App\Domains\Reimbursement\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Travel', 'description' => 'Transport and travel expenses', 'requires_receipt' => true, 'approval_levels' => 2, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Meals', 'description' => 'Meal and entertainment', 'requires_receipt' => true, 'approval_levels' => 1, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Medical', 'description' => 'Health and medical expenses', 'requires_receipt' => true, 'approval_levels' => 2, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Training', 'description' => 'Courses and certifications', 'requires_receipt' => true, 'approval_levels' => 3, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Office Supplies', 'description' => 'Work equipment and supplies', 'requires_receipt' => false, 'approval_levels' => 1, 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
