<!-- Scripts are already loaded in head.php -->

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


<!-- DataTables Buttons extension JS (for export functionality) -->
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<!-- Libraries for Exporting Excel and PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vfs-fonts/2.0.0/vfs_fonts.min.js"></script>

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


<script src="<?= Yii::$app->request->baseUrl ?>/web/js/ss.custom.js"></script>

<!-- <script src="https://demo.smart-school.in/backend/dist/js/nprogress.js"></script>
<script type="text/javascript" src="https://demo.smart-school.in/backend/dist/datatables/js/ss.custom.js"></script> -->