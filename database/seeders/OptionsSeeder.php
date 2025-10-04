<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;

class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            // Occupation
            [
                'category'   => 'profile',
                'title'      => 'occupation',
                'text'       => "I'm in a",
                'input_type' => 'dropdown',
                'value'      => [
                    'Work',
                    'School',
                    'Freelance',
                    'Unemployed',
                    'Student',
                    'Entrepreneur',
                    'Retired',
                    'Homemaker',
                    'Internship',
                    'Part-time',
                ],
                'sort_order' => 1,
            ],

            // Age (18–70 step 1 year for realism)
            [
                'category'   => 'profile',
                'title'      => 'age',
                'text'       => 'In',
                'input_type' => 'dropdown',
                'value'      => range(18, 70),
                'sort_order' => 2,
            ],

            // Gender
            [
                'category'   => 'profile',
                'title'      => 'gender',
                'text'       => 'Looking to meet a',
                'input_type' => 'dropdown',
                'value'      => [
                    'Male',
                    'Female',
                    'Non-binary',
                    'Transgender',
                    'Other',
                    'All',
                ],
                'sort_order' => 3,
            ],

            // Status
            [
                'category'   => 'profile',
                'title'      => 'status',
                'text'       => 'Status',
                'input_type' => 'dropdown',
                'value'      => [
                    'Single',
                    'Taken',
                    'Complicated',
                    'Divorced',
                    'Widowed',
                    'Separated',
                    'Secret',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($options as $opt) {
            Option::create($opt);
        }
    }
}
