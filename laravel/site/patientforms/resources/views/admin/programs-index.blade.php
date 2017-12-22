@extends('layouts.app-no-sidebar')

@section('title', 'Programs')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id="app">
        <h1>Program Listing</h1>
        <hr>
        <p class=''>
            <a class="btn btn-primary " href='{{ route('programs.create') }}'>Create New Program</a>
        </p>
        <div class="program-listing">
            @forelse ($programs as $program)
                @include('programs.program-listing')
            @empty
                No programs
            @endforelse
        </div>

        <div class="modal fade" tabindex="-1" id="delete-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-real-content">
                </div><!-- /.modal-content -->
                <div class="modal-content modal-loading">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirm Deletion</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src={{ asset("images/ripple.svg") }}><br>
                            <small class="text-muted">Loading...</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@endsection