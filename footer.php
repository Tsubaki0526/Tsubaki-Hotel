
<script>
var LANG = {
    complete_fields: '<?php echo __('ajax_complete_fields'); ?>',
    employee_added: '<?php echo __('ajax_employee_added'); ?>',
    id_cedula: '<?php echo __('ajax_id_cedula'); ?>',
    id_voto: '<?php echo __('ajax_id_voto'); ?>',
    id_pan: '<?php echo __('ajax_id_pan'); ?>',
    id_license: '<?php echo __('ajax_id_license'); ?>',
    pending: '<?php echo __('ajax_pending'); ?>',
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggler = document.querySelector('.navbar-toggler');
    var sidebar = document.querySelector('.sidebar');
    if (toggler && sidebar) {
        toggler.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !toggler.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
});
</script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/foundation-datepicker.min.js"></script>
<script src="js/validator.min.js"></script>
<script src="js/custom.js"></script>
<script src="ajax.js"></script>

</body>
</html>
