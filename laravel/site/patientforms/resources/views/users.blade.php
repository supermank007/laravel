@extends('layouts.app')

@section('title', 'Users')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <h1>User Listing</h1>
    <ul>
        @forelse ($users as $user)
            <li>User email: {{ $user->email }}, Role: {{ $user->roles()->first()->role }}
            </li>
        @empty
            <li>No users</li>
        @endforelse
    </ul>
    <p>
        <a class="btn btn-default" href='{{ route('create_user') }}'>Create New User</a>
    </p>
@endsection