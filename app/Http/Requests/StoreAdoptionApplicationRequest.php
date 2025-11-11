<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdoptionApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'exists:pets,id'],
            'living_situation' => ['required', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:2000'],
            'other_pets' => ['nullable', 'string', 'max:2000'],
            'veterinary_reference' => ['nullable', 'string', 'max:255'],
            'household_members' => ['nullable', 'string', 'max:2000'],
            'employment_status' => ['nullable', 'string', 'max:255'],
            'reason_for_adoption' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pet_id.required' => 'Please select a pet to adopt.',
            'pet_id.exists' => 'The selected pet is no longer available.',
            'living_situation.required' => 'Please describe your living situation.',
            'living_situation.max' => 'Living situation must not exceed 255 characters.',
            'experience.max' => 'Experience description must not exceed 2000 characters.',
            'other_pets.max' => 'Other pets description must not exceed 2000 characters.',
            'veterinary_reference.max' => 'Veterinary reference must not exceed 255 characters.',
            'household_members.max' => 'Household members description must not exceed 2000 characters.',
            'employment_status.max' => 'Employment status must not exceed 255 characters.',
            'reason_for_adoption.required' => 'Please tell us why you want to adopt this pet.',
            'reason_for_adoption.max' => 'Reason for adoption must not exceed 2000 characters.',
        ];
    }
}
