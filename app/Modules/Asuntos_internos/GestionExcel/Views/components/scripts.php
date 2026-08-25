<script src="<?= base_url('assets/asuntos_internos/gestion_excel/js/main.js') ?>"></script>

<?php if (!empty($js)): ?>

    <?php foreach ($js as $archivo): ?>

        <script src="<?= base_url('assets/asuntos_internos/gestion_excel/js/' . $archivo) ?>"></script>

    <?php endforeach; ?>

<?php endif; ?>
