<h1>Patient Forms Reminder</h1>
<p>Hello, you have a form waiting to be completed.</p>
<p>Please complete <a href='{{ URL::to('/forms/take/' . $form->id) }}'>{{ $form->name }}</a> on <a href='{{ URL::to('/') }}'>{{ URL::to('/') }}</a></p>