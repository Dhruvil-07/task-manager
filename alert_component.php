<?php if (isset($_SESSION['alert'])): ?>
    <div id="alertBox" class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 9999; min-width: 300px;">
        <?= $_SESSION['alert']['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('alertBox');
            if (alertBox) {
                // Trigger Bootstrap fade-out class
                alertBox.classList.remove('show');
                alertBox.classList.add('fade');

                // Fully remove from DOM after fade
                setTimeout(() => {
                    alertBox.remove();
                }, 500);
            }
        }, 3000);
    </script>

    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
