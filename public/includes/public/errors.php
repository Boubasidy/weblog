<?php if (!empty($errors)): ?>
  <div class="error-message" style="color: red;">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?php echo htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
