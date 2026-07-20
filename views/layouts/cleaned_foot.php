
<!-- jQuery -->
<script src="assets/js/jquery-2.1.4.min.js"></script> <!-- Core jQuery -->

<!-- Bootstrap -->
<script src="assets/js/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="assets/js/jquery-ui.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>

<!-- Moment.js -->
<script src="assets/js/moment.min.js"></script>

<!-- Date & Time Pickers -->
<script src="assets/js/bootstrap-datepicker.min.js"></script>
<script src="assets/js/bootstrap-timepicker.min.js"></script>
<script src="assets/js/daterangepicker.min.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<script src="assets/js/bootstrap-colorpicker.min.js"></script>

<!-- Chosen and Select2 -->
<script src="assets/js/chosen.jquery.min.js"></script>
<script src="assets/js/select2.min.js"></script>

<!-- Plugins for file uploads, tags, and text areas -->
<script src="assets/js/dropzone.min.js"></script>
<script src="assets/js/autosize.min.js"></script>
<script src="assets/js/jquery.inputlimiter.min.js"></script>
<script src="assets/js/jquery.maskedinput.min.js"></script>
<script src="assets/js/bootstrap-tag.min.js"></script>

<!-- Dual Listbox & Multiselect -->
<script src="assets/js/jquery.bootstrap-duallistbox.min.js"></script>
<script src="assets/js/bootstrap-multiselect.min.js"></script>

<!-- Typeahead for autocomplete -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.1/typeahead.bundle.min.js"></script>

<!-- ACE Admin Template -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>

<!-- Inline scripts -->
<script type="text/javascript">
    jQuery(function($) {
        // Chosen select
        if (!ace.vars['touch']) {
            $('.chosen-select').chosen({ allow_single_deselect: true });
            $(window).on('resize.chosen', function() {
                $('.chosen-select').each(function() {
                    $(this).next().css({'width': '100%'});
                });
            }).trigger('resize.chosen');
        }

        // Dual Listbox
        $('select[name="duallistbox_demo1[]"]').bootstrapDualListbox();

        // Autosize textareas
        autosize($('textarea[class*=autosize]'));

        // Datepicker
        $('.date-picker').datepicker({
            autoclose: true,
            todayHighlight: true
        });

        // Input limiter
        $('input.limited').inputlimiter({
            limit: 10
        });

        // Destroy plugins when the page is unloaded
        $(document).one('ajaxloadstart.page', function(e) {
            autosize.destroy('textarea[class*=autosize]');
            $('.chosen-container').remove();
            $('.daterangepicker.dropdown-menu').remove();
        });
    });
</script>
