<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Confirm Deletion</h4>
</div>
<div class="modal-body">
    <p>
        Are you sure you want to delete the program "{{ $currentProgram->name }}"?<br>
    </p>
    @if (count($users))
        <p>
            <strong class="text-warning">Warning: This program has {{ count_plural('user', 'users', $users) }}!</strong>
        </p>
        <p>
            I would like to:<br>
            <form id="program-users-form">
                <label>
                    <input type="radio" name="program-users-action" value="move_users" checked>
                    Move the program's users to another program:
                    <select id="program-users-new-program" name="program-users-new-program">
                        @foreach ($programs as $program)
                            @if ($currentProgram == $program)
                                @continue
                            @endif
                            <option value='{{ $program->id }}'>{{ $program->name }}</option>
                        @endforeach
                    </select>
                </label><br>
                <label>
                    <input type="radio" name="program-users-action" value="delete_users">
                    Delete the program's users
                </label>
            </form>
        </p>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="button" data-loading-text="Deleting..." class="btn btn-primary" id="delete-program-confirm" data-program-id=''>Delete</button>
</div>