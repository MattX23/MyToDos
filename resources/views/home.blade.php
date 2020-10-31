@extends('layouts.app')

@section('content')
<home-page
    :user-id="{{ Auth::user()->id }}"
></home-page>
@endsection
