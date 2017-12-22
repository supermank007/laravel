<table class="table table-striped table-bordered table-no-inside-border">
    <colgroup>
        <col>
        <col>
        <col>
        <col>
        @if ($currentUser && $currentUser->hasRoles('Admin', 'Super-Admin', 'Master-Admin'))
            <col>
            <col>
        @endif
        @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
            <col>
        @endif
        <col style="width:10%">
    </colgroup>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th># Questions</th>
            @if ($currentUser && $currentUser->hasRoles('Admin', 'Super-Admin', 'Master-Admin'))
                <th>Created By</th>
                <th>Last Edited By</th>
            @endif
            @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                <th>Published</th>
            @endif
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($forms as $form)
            <tr class="form-listing {{ ($form->archived && $currentUser->hasRoles('Super-Admin', 'Master-Admin')) ? 'bg-warning' : '' }}" data-form-id="{{ $form->id }}">

                <td>{{ $form->id }}</td>

                <td>
                    {{ $form->name }}
                    @if ($form->inProgressByUserRegistration($currentUserRegistration))
                        <span class="label label-primary">In-Progress</span>
                    @endif
                    {{ ($form->archived && $currentUser->hasRoles('Super-Admin', 'Master-Admin')) ? ' (Archived)' : '' }}
                </td>

                <td>{{ $form->description }}</td>

                <td>{{ $form->questions()->count() }}</td>

                @if ($currentUser && $currentUser->hasRoles('Admin', 'Super-Admin', 'Master-Admin'))
                    <td>{{ $form->creator_user->fullName() }}</td>
                    <td>{{ $form->editor_user->fullName() }}</td>
                @endif

                @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                    <td>
                        <input type='checkbox' name='form_published' @if ($form->published) checked @endif @click="formPublishedState">
                    </td>
                @endif
                
                <td style='white-space: nowrap' class="text-right">

                    @if ( $form->isTakeableByUserRegistration($currentUserRegistration) )
                        @if ($form->inProgressByUserRegistration($currentUserRegistration))
                            <a href='@relative_route("forms.resume", ['form_id' => $form->id])' class="btn btn-success btn-sm form-take">
                                Resume
                            </a>
                        @else
                            <a href='@relative_route("forms.take", ['form_id' => $form->id])' class="btn btn-success btn-sm form-take">
                                Take
                            </a>
                        @endif
                    @endif

                    <div class="btn-group form-actions-group" role="group" aria-label="Form Actions">
                        @if (! $form->archived)
                            @if ($currentUser && $currentUser->hasRoles('Admin') && $form->published)
                                <a href='@relative_route("forms.assign", ['f' => $form->id])' class="btn btn-default btn-sm form-assign">
                                    Assign
                                </a>
                            @endif

                            @if (! $form->published && $currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                                <a href='@relative_route("forms.edit", ['form' => $form->id])' class="btn btn-default btn-sm form-edit">
                                    Edit
                                </a>
                            @endif

                            @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                                <a href='#' class='btn btn-default btn-sm form-duplicate' @click="formDuplicate" data-loading-text="Loading..." data-default-text="Duplicate">
                                    Duplicate
                                </a>
                            @endif
                        @endif

                        @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                            @if (! $form->archived)
                                <a href='#' class="btn btn-default btn-sm form-archive" @click="formArchive" data-loading-text="Loading..." data-default-text="Archive">
                                    Archive
                                </a>
                            @else
                                <a href='#' class="btn btn-default btn-sm form-unarchive" @click="formUnarchive" data-loading-text="Loading..." data-default-text="Unarchive">
                                    Unarchive
                                </a>
                            @endif
                        @endif
                    </div>

                    @if ($currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                        @if ($form->isDeletable())
                            <a href='#' class="btn btn-danger btn-sm form-delete" @click="formDelete" data-loading-text="Loading..." data-default-text="Delete">
                                Delete
                            </a>
                        @endif
                    @endif

                </td>
            </tr>
        @empty
            <td colspan='8'>No forms</td>
        @endforelse
    </tbody>
</table>