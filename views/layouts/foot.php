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
            $('.chosen-select').chosen({
                allow_single_deselect: true
            });
            $(window).on('resize.chosen', function() {
                $('.chosen-select').each(function() {
                    $(this).next().css({
                        'width': '100%'
                    });
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


<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons extension JS -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>

<!-- Libraries for Exporting Excel and PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> <!-- For Excel -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<!-- For Excel, CSV, and PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> <!-- For PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vfs-fonts/2.0.0/vfs_fonts.min.js"></script> <!-- For PDF -->

<!-- DataTables Initialization Script -->
<script type="text/javascript">
    $.noConflict();
    jQuery(function() {
        var $dt = jQuery;

        if (typeof $dt.fn.DataTable === 'undefined') {
            return;
        }

        var $table = $dt('#dynamic-table');
        if ($table.length === 0) {
            return;
        }

        var myTable = $table.DataTable({
            paging: true,
            ordering: true,
            info: true,
            dom: 'Bfrtip',
            buttons: [{
                    "extend": "copy",
                    "text": "<i class='fa fa-copy bigger-110 pink'></i>",
                    "className": "btn btn-white btn-primary btn-bold"
                },
                {
                    "extend": "csv",
                    "text": "<i class='fa fa-database bigger-110 orange'></i>",
                    "className": "btn btn-white btn-primary btn-bold"
                },
                {
                    "extend": "pdf",
                    "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i>",
                    "className": "btn btn-white btn-primary btn-bold"
                }
            ]
        });

        var $toolsContainer = $dt('.tableTools-container');
        if ($toolsContainer.length > 0) {
            myTable.buttons().container().appendTo($toolsContainer);
        }
    });
</script>


<script src="assets/js/nprogress.js"></script>
<script src="assets/js/ss.custom.js"></script>

<!-- <script src="https://demo.smart-school.in/backend/dist/js/nprogress.js"></script>
<script type="text/javascript" src="https://demo.smart-school.in/backend/dist/datatables/js/ss.custom.js"></script> -->