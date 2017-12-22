@extends('layouts.app-no-sidebar')

@section('title', 'Choose Registration')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Choose Registration</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="" method="POST" class="registration-select-form">
                    <table class="table table-striped">
                        <colgroup>
                            <col width="75%">
                            <col width="25%">
                        </colgroup>
                        <thead>
                            <th>Registration Number</th>
                            <th># of Unfinished Forms</th>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr class="user-registration" data-registration-number="{{ $registration->registration_number }}">
                                    <td>{{ $registration->registration_number }}</td>
                                    <td>{{ $registration->num_unfinished_forms }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <input type="hidden" name="registration_number" value="" id="registration_number">
                </form>
            </div>
        </div>
    </div>

@endsection