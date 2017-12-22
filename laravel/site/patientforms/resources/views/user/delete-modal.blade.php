<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Confirm Deletion</h4>
</div>
<div class="modal-body">
    <p>
        Are you sure you want to delete the user "{{ $user->email }}"?<br>
    </p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="button" data-loading-text="Deleting..." class="btn btn-primary" id="delete-user-confirm" data-user-id=''>Delete</button>
</div>