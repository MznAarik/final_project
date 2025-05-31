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
            'name' => 'required|string|min:3',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            'capacity' => 'required|integer|min:1',
            'ticket_price' => 'required|numeric|min:0',
            'contact_info' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'required|string',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'organizer' => 'required|string|max:255',
            'tickets_sold' => 'nullable|integer|min:0',
            'currency' => 'required|in:USD,EUR,NPR|regex:/^[A-Z]{3}$/',
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
            'ticket_price.required' => 'Ticket price is required and must be a non-negative number.',
            'contact_info.required' => 'Contact information is required and must not exceed 255 characters.',
            'start_date.required' => 'Start date is required and must be today or later.',
            'end_date.required' => 'End date is required and must be after the start date.',
            'category.required' => 'Category is required and must be one of the predefined options.',
            'status.required' => 'Status is required and must be one of the predefined options.',
            'organizer.required' => 'Organizer information is required and must not exceed 255 characters.',
            'currency.required' => 'Currency is required and must be a 3-letter code.',
        ];
    }
}
