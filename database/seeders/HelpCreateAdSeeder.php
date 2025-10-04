<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;

class HelpCreateAdSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            [
                'category'   => 'help_create_ad',
                'title'      => 'height',
                'text'       => "I'm",
                'input_type' => 'dropdown',
                'value'      => [
                    'Short',
                    'Average',
                    'Tall',
                    'Very Tall',
                ],
                'sort_order' => 1,
            ],
            [
                'category'   => 'help_create_ad',
                'title'      => 'hair',
                'text'       => null,
                'input_type' => 'dropdown',
                'value'      => [
                    'Blonde Hair',
                    'Brown Hair',
                    'Black Hair',
                    'Red Hair',
                    'Blue Hair',
                    'Green Hair',
                    'Gray Hair',
                    'Bald',
                ],
                'sort_order' => 2,
            ],
            [
                'category'   => 'help_create_ad',
                'title'      => 'eyes',
                'text'       => 'And',
                'input_type' => 'dropdown',
                'value'      => [
                    'Blue Eyes',
                    'Brown Eyes',
                    'Green Eyes',
                    'Gray Eyes',
                    'Hazel Eyes',
                    'Amber Eyes',
                ],
                'sort_order' => 3,
            ],
            [
                'category'   => 'help_create_ad',
                'title'      => 'behavior',
                'text'       => 'Eyes, Who',
                'input_type' => 'dropdown',
                'value'      => [
                    'Shy',
                    'Confident',
                    'Adventurous',
                    'Romantic',
                    'Funny',
                    'Serious',
                    'Friendly',
                    'Calm',
                ],
                'sort_order' => 4,
            ],
            [
                'category'   => 'help_create_ad',
                'title'      => 'seeking',
                'text'       => 'Seeking',
                'input_type' => 'dropdown',
                'value'      => [
                    'Friendship',
                    'Romance',
                    'Casual Dating',
                    'Serious Relationship',
                    'Adventure Partner',
                    'Marriage',
                ],
                'sort_order' => 5,
            ],
            [
                'category'   => 'help_create_ad',
                'title'      => 'hobby',
                'text'       => 'Into',
                'input_type' => 'dropdown',
                'value'      => [
                    'Sports',
                    'Music',
                    'Travel',
                    'Cooking',
                    'Reading',
                    'Movies',
                    'Art',
                    'Gaming',
                    'Dancing',
                    'Outdoors',
                ],
                'sort_order' => 6,
            ],
        ];

        foreach ($options as $opt) {
            Option::create($opt);
        }
    }
}
