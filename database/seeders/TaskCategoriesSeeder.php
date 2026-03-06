<?php

namespace Database\Seeders;


use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskCategories = [
            ['name' => 'Todo'],
            ['name' => 'InProgress'],
            ['name' => 'Testing'],
            ['name' => 'Done'],
            ['name' => 'Pending'],
        ];

        foreach ($taskCategories as $category) {
            Category::create($category);
        }
    }
}
