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
            'status' => 'nullable|string|in:upcoming,active,completed,cancelled',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string',
            'contact_info' => 'required|email',
            'start_date' => 'required|date|after:today|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'organizer' => 'required|string|max:255',
            'tickets_sold' => 'nullable|integer|min:0',
            'event_category' => 'nullable|string|max:50',
            'ticket_category_price' => 'required|array',
            'ticket_category_price.*.category' => 'required|string|max:50',
            'ticket_category_price.*.price' => 'required|numeric|min:0',
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
            'status.in' => 'Status must be one of the following: upcoming, active, completed, or cancelled.',
            'ticket_category_price.required' => 'Ticket category and price information is required.',
            'organizer.required' => 'Organizer information is required and must not exceed 255 characters.',
        ];
    }
}
