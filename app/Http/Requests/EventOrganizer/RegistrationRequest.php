<?php

namespace App\Http\Requests\EventOrganizer;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'org_name' => ['required', 'string', 'max:255'],
            'org_email' => ['required', 'string', 'email', 'max:255', 'unique:event_organizers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'description' => ['nullable', 'string'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*.type' => ['required', 'string', 'in:business_license,company_profile,previous_event_proof'],
            'documents.*.file' => ['required', 'file', 'max:10240'], // 10MB max
            'documents.*.notes' => ['nullable', 'string']
        ];
    }
}
