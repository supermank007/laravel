@extends('layouts.app-no-sidebar')

@section('title', 'Home')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Dashboard</h1>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Recent Forms</h3>
            </div>
            <div class="panel-body">
                @if (count($forms))
                    <table class="table table-striped table-bordered table-no-inside-border">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col width="5%">
                        </colgroup>
                        <thead>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th># Questions</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($forms as $form)
                                <tr>
                                    <td>{{ $form->id }}</td>
                                    <td>
                                        {{ $form->name }}
                                        @if ($form->inProgressByUserRegistration($currentUserRegistration))
                                            <span class="label label-primary">In-Progress</span>
                                        @endif
                                    </td>
                                    <td>{{ $form->description }}</td>
                                    <td>{{ $form->questions()->count() }}</td>
                                    <td>
                                        @if ($form->isTakeableByUserRegistration($currentUserRegistration))
                                            <a href='{{ $form->inProgressByUserRegistration($currentUserRegistration) ? route("forms.resume", ['form_id' => $form->id], null) : route("forms.take", ['form_id' => $form->id], null) }}' class="btn btn-success form-take">
                                                {{ $form->inProgressByUserRegistration($currentUserRegistration) ? 'Resume' : 'Take' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href='@relative_route('forms.index')' class="btn btn-default btn-lg">View All Forms</a>
                @else
                    <p class="text-muted">No recent forms to show</p>
                @endif
            </div>
        </div>
    </div>
@endsection