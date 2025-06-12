<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventsValidate extends FormRequest
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
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            // 'district' => 'required|string|max:255',
            // 'province' => 'required|string|max:255',
            // 'country' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string',
            'contact_info' => 'required|email',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'required|string',
            'status' => 'required|in:active,inactive,cancelled',
            'organizer' => 'required|string|max:255',
            'tickets_sold' => 'nullable|integer|min:0',

            'ticket_category' => 'required|string|max:25|min:1',
            // 'ticket_category.*' => 'required|string|max:50',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Event name is required and must be at least 3 characters.',
            'venue.required' => 'Venue is required and must not exceed 255 characters.',
            'location.required' => 'Location is required and must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'image.image' => 'Image must be a valid image file.',
            'capacity.required' => 'Capacity is required and must be a positive integer.',
            'contact_info.required' => 'Contact information is required and must not exceed 255 characters.',
            'start_date.required' => 'Start date is required and must be today or later.',
            'end_date.required' => 'End date is required and must be after the start date.',
            // 'category.required' => 'Category is required and must be one of the predefined options.',
            'status.required' => 'Status is required and must be one of the predefined options.',
            'organizer.required' => 'Organizer information is required and must not exceed 255 characters.',
        ];
    }
}
