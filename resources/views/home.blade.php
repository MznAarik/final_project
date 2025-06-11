@extends('layouts.app')


@section('title', 'Home')

@section('content')

    @include('components.hero')

    @include('components.event_section', ['title' => 'Latest Events', 'seeMoreLink' => '/buy_tickets'])
    @include('components.event_section', ['title' => 'Most Popular', 'seeMoreLink' => '/popular'])
    @include('components.event_section', ['title' => 'Upcoming Events', 'seeMoreLink' => '/upcoming'])

@endsection