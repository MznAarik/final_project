<div>
    {!! NoCaptcha::display() !!}
    @if ($errors->has('g-recaptcha-response'))
        <span class="text-red-500 text-sm">
            {{ $errors->first('g-recaptcha-response') }}
        </span>
    @endif
</div>

{!! NoCaptcha::renderJs() !!}