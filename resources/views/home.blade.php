@extends('layouts.app')


@section('title', $title ?? 'BooKets')

@section('content')

    @include('components.hero')

    @include('components.event_section', ['seeMoreLink' => '/buy_tickets'])

@endsection