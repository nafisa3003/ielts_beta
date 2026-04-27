<?php // includes/footer.php ?>
<?php foreach ($extra_js ?? [] as $js): ?>
<script src="/ielts_beta_v3/assets/js/<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>
<?php if (!empty($inline_js)): ?>
<script><?= $inline_js ?></script>
<?php endif; ?>
</body>
</html>
