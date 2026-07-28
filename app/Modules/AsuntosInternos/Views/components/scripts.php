<script src="<?= base_url('assets/asuntos_internos/js/main.js') ?>"></script>

<?php if (!empty($js)): ?>

    <?php foreach ($js as $archivo): ?>

        <script src="<?= base_url('assets/asuntos_internos/js/' . $archivo) ?>"></script>

    <?php endforeach; ?>

<?php endif; ?>