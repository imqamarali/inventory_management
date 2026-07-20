<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/bootstrap.min.css',
        'font-awesome/4.5.0/css/font-awesome.min.css',
        'css/jquery-ui.custom.min.css',
        'css/jquery-ui.min.css',
        'css/bootstrap-datepicker3.min.css',
        'css/bootstrap-timepicker.min.css',
        'css/daterangepicker.min.css',
        'css/bootstrap-datetimepicker.min.css',
        'css/bootstrap-colorpicker.min.css',
        'css/fullcalendar.min.css',
        'css/dropzone.min.css',
        'css/bootstrap-duallistbox.min.css',
        'css/bootstrap-multiselect.min.css',
        'css/select2.min.css',
        'css/chosen.min.css',
        'css/ui.jqgrid.min.css',
        'css/jquery.gritter.min.css',
        'css/bootstrap-editable.min.css',
        'css/prettify.min.css',
        'css/ace.min.css',
        'css/ace-part2.min.css',
        'css/ace-skins.min.css',
        'css/ace-rtl.min.css',
        'css/ace-ie.min.css',
        'css/fonts.googleapis.com.css',
    ];

    public $js = [
        // Core JS libraries
        'js/jquery-2.1.4.min.js',                  // jQuery 2.1.4
        'js/jquery-1.11.3.min.js',                 // jQuery 1.11.3 (if needed for compatibility)
        'js/jquery-ui.min.js',                     // jQuery UI
        'js/jquery-ui.custom.min.js',              // jQuery UI Custom
        'js/jquery.ui.touch-punch.min.js',         // jQuery UI Touch Punch
        'js/moment.min.js',                        // Moment.js (required for DatePicker, FullCalendar, etc.)

        // jQuery Validation
        'js/jquery.validate.min.js',               // jQuery Validate
        'js/jquery-additional-methods.min.js',     // jQuery Additional Methods (after Validate)

        // Bootstrap and related plugins
        'js/bootstrap.min.js',                     // Bootstrap JS
        'js/bootstrap-colorpicker.min.js',         // Bootstrap Colorpicker
        'js/bootstrap-datepicker.min.js',          // Bootstrap Datepicker
        'js/bootstrap-datetimepicker.min.js',      // Bootstrap Datetimepicker (Moment.js required)
        'js/bootstrap-editable.min.js',            // Bootstrap Editable
        'js/bootstrap-markdown.min.js',            // Bootstrap Markdown
        'js/bootstrap-multiselect.min.js',         // Bootstrap Multiselect
        'js/bootstrap-tag.min.js',                 // Bootstrap Tag
        'js/bootstrap-timepicker.min.js',          // Bootstrap Timepicker
        'js/bootstrap-wysiwyg.min.js',             // Bootstrap WYSIWYG

        // Ace Template JS
        'js/ace.min.js',                           // Ace Main
        'js/ace-extra.min.js',                     // Ace Extra
        'js/ace-elements.min.js',                  // Ace Elements
        'js/ace-editable.min.js',                  // Ace Editable

        // DataTables and related plugins
        'js/jquery.dataTables.min.js',             // DataTables
        'js/jquery.dataTables.bootstrap.min.js',   // DataTables Bootstrap
        'js/dataTables.buttons.min.js',            // DataTables Buttons
        'js/buttons.flash.min.js',                 // Buttons Flash
        'js/buttons.html5.min.js',                 // Buttons HTML5
        'js/buttons.print.min.js',                 // Buttons Print
        'js/buttons.colVis.min.js',                // Buttons ColVis
        'js/dataTables.select.min.js',             // DataTables Select

        // Additional jQuery plugins
        'js/jquery.bootstrap-duallistbox.min.js',  // Bootstrap Dual Listbox
        'js/jquery.colorbox.min.js',               // Colorbox
        'js/jquery.easypiechart.min.js',           // Easy Pie Chart
        'js/jquery.flot.min.js',                   // Flot
        'js/jquery.flot.pie.min.js',               // Flot Pie
        'js/jquery.flot.resize.min.js',            // Flot Resize
        'js/jquery.gritter.min.js',                // Gritter
        'js/jquery.hotkeys.index.min.js',          // jQuery Hotkeys
        'js/jquery.inputlimiter.min.js',           // jQuery Input Limiter
        'js/jquery.jqGrid.min.js',                 // jQuery jqGrid
        'js/jquery.knob.min.js',                   // jQuery Knob
        'js/jquery.maskedinput.min.js',            // jQuery Masked Input
        'js/jquery.nestable.min.js',               // jQuery Nestable
        'js/jquery.raty.min.js',                   // jQuery Raty
        'js/jquery.sparkline.index.min.js',        // Sparkline

        // Miscellaneous plugins
        'js/bootbox.js',                           // Bootbox.js
        'js/autosize.min.js',                      // Autosize
        'js/markdown.min.js',                      // Markdown
        'js/prettify.min.js',                      // Prettify
        'js/spin.js',                              // Spin.js
        'js/spinbox.min.js',                       // Spinbox
        'js/tree.min.js',                          // Tree
        'js/wizard.min.js',                        // Wizard

        'js/dropzone.min.js',                      // Dropzone
        'js/excanvas.min.js',                      // ExCanvas
        'js/fullcalendar.min.js',                  // FullCalendar (requires Moment.js)
        'js/grid.locale-en.js',                    // Grid Locale EN
        'js/holder.min.js',                        // Holder.js
        'js/respond.min.js',                       // Respond.js (for IE compatibility)
        'js/daterangepicker.min.js',               // Date Range Picker (requires Moment.js)
        'js/select2.min.js',                       // Select2
    ];




    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
