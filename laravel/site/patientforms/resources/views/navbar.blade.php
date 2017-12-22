<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="@relative_route('index')">Patient Forms
                @if ($app->environment('local'))
                    <sup style="opacity:0.7; color:darksalmon;" class="env-dev">Dev v{{ config('app.version') }}</sup>
                @endif
            </a>
            
        </div>

        <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
            @if ( $currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin') )
                <a class="btn btn-primary navbar-btn pull-left" href="@relative_route('forms.create')"><i class="glyphicon glyphicon-plus-sign"></i> New Form</a>
            @endif
            <ul class="nav navbar-nav">
                @if ($currentUser)
                        <li><a href="@relative_route('forms.index')">Forms</a></li>
                    @if ( $currentUser->hasRoles('Super-Admin', 'Master-Admin') )
                        <li><a href="@relative_route('users.index')">Users</a></li>
                        <li><a href="@relative_route('programs.index')">Programs</a></li>
                    @endif
                    <li>
                        <span class="divider-vertical"></span>
                    </li>
                    <li>
                        <span class="user-account-link">
                            <a href='{{ route("users.account", ["user" => $currentUser->id], null) }}' title='View account'>
                                <span class="glyphicon glyphicon-user navbar-user-icon"></span>
                                {{ $currentUser->email }}</a>
                            @if ($currentUser->hasRoles('user'))
                                <a href='@relative_route("select_registration")' title='Change Registration'>
                                    <span class="badge navbar-user-registration">
                                        @if ($currentUserRegistration)
                                            #{{ $currentUserRegistration->registration_number }}
                                        @else
                                            (No registration)
                                        @endif
                                    </span>
                                </a>
                            @endif
                        </span>
                    </li>
                @endif
            </ul>
            @if ($currentUser)
                <a class="btn btn-default navbar-btn" href="@relative_route('logout')">Logout</a>
            @else
                <a class="btn btn-default navbar-btn" href="@relative_route('login')">Login</a>
            @endif
        </div><!-- /.navbar-collapse -->
    </div>
</nav>