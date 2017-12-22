<div class="panel panel-default program" data-program-id="{{ $program->id }}">
    <div class="panel-heading">
        <h3 class="pull-left">{{ $program->name }}</h3>

        <span class="panel-btn-group pull-right">
            <span class='panel-btn program-edit'>
                <a class="glyphicon glyphicon-pencil" data-toggle="tooltip" title='Edit Program' aria-hidden="true" href='@relative_route('programs.edit', ['program' => $program->id])'></a>
            </span>
            <span class='panel-btn program-delete'>
                <a class="glyphicon glyphicon-remove" data-toggle="tooltip" title='Delete Program' aria-hidden="true" href='#'></a>
            </span>
        </span>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <p><strong>Description:</strong> {{ $program->description }}</p>
        <small class='text-muted'><strong>Program ID:</strong> {{ $program->id }}</small>
    </div>
</div>