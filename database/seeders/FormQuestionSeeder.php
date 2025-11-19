<?php

namespace Database\Seeders;

use App\Enums\FormType;
use App\Enums\QuestionType;
use App\Models\FormQuestion;
use Illuminate\Database\Seeder;

class FormQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adoptionQuestions = [
            [
                'label' => 'What is your living situation?',
                'type' => QuestionType::Dropdown,
                'options' => [
                    'House with yard',
                    'Apartment',
                    'Condo',
                    'Farm',
                    'Other',
                ],
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Do you have experience with pets?',
                'type' => QuestionType::Textarea,
                'options' => null,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'label' => 'Do you have any other pets in your household?',
                'type' => QuestionType::Textarea,
                'options' => null,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'label' => 'Veterinary reference (name and phone number)',
                'type' => QuestionType::String,
                'options' => null,
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'label' => 'Who else lives in your household?',
                'type' => QuestionType::Textarea,
                'options' => null,
                'is_required' => false,
                'sort_order' => 5,
            ],
            [
                'label' => 'What is your employment status?',
                'type' => QuestionType::Dropdown,
                'options' => [
                    'Employed Full-time',
                    'Employed Part-time',
                    'Self-employed',
                    'Retired',
                    'Student',
                    'Other',
                ],
                'is_required' => false,
                'sort_order' => 6,
            ],
            [
                'label' => 'Why do you want to adopt this pet?',
                'type' => QuestionType::Textarea,
                'options' => null,
                'is_required' => true,
                'sort_order' => 7,
            ],
            [
                'label' => 'Do you have a fenced yard?',
                'type' => QuestionType::Switch,
                'options' => null,
                'is_required' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($adoptionQuestions as $questionData) {
            FormQuestion::create([
                'form_type' => FormType::Adoption,
                'label' => $questionData['label'],
                'type' => $questionData['type'],
                'options' => $questionData['options'],
                'is_required' => $questionData['is_required'],
                'sort_order' => $questionData['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
