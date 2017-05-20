<?php
$successes = Messages::getSuccesses();
if (count($successes) > 0):
?>
<div class="success-msg">
    <ul>
        <?php foreach ($successes as $msg): ?>
        <li><?php echo $msg; ?></li>
        <?php endforeach; ?>
  </ul>
</div>
<?php
endif;

$errors = Messages::getErrors();
if (count($errors) > 0):
?>
<div class="error-msg">
    <ul>
        <?php foreach ($errors as $msg): ?>
        <li><?php echo $msg; ?></li>
        <?php endforeach; ?>
  </ul>
</div>
<?php
endif;