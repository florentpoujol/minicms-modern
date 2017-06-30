<?php
$successes = \App\Messages::getSuccesses();
?>
@if (count($successes) > 0)
<div class="success-msg">
    <ul>
        @foreach ($successes as $msg)
        <li>{$msg}</li>
        @endforeach
  </ul>
</div>
@endif

<?php
$errors = \App\Messages::getErrors();
?>
@if (count($errors) > 0)
<div class="error-msg">
    <ul>
        @foreach ($errors as $msg)
        <li>{$msg}</li>
        @endforeach
  </ul>
</div>
@endif

