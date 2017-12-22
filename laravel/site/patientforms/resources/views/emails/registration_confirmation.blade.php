<h1>Patient Forms Registration Confirmation</h1>
<p>Hello {{ $email }}, you have been successfully registered on Patient Forms.</p>
<p><strong>Registration Number:</strong> {{ $registration_number }}</p>
<p><a href='{{ URL::to('/login') }}'>Patient Forms Login</a></p>