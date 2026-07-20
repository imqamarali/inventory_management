<style>
/* Fixed footer at bottom with sidebar color */
.footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    z-index: 999 !important;
    margin: 0 !important;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
}

/* Add padding to main-content to prevent content from being hidden behind footer */
.main-content {
    padding-bottom: 60px !important;
}

/* Ensure footer content is visible */
.footer-inner {
    background-color: inherit;
}
</style>

<div class="footer" id="footer" style="padding-top: 40px;">
    <div class="footer-inner">
        <div class="footer-content" style="position: absolute;
            left: 12px;
            right: 12px;
            bottom: 4px;
            padding: 8px;
            line-height: 20px;
            border-top: 3px double #E5E5E5;">
            <span class="bigger-120">
                <?php
                $cms_settings = Yii::$app->Component->CMS(); ?>
                <span class="blue bolder">Inventory Management System</span>
            </span>

            &nbsp; &nbsp;
            <span class="action-buttons">
                <?php if (!empty($cms_settings['twitter_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['twitter_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['facebook_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['facebook_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['linkedin_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['linkedin_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-linkedin-square bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['instagram_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['instagram_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-instagram bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['youtube_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['youtube_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-youtube-square red bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['pinterest_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['pinterest_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-pinterest-square bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['whatsapp_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['whatsapp_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-whatsapp bigger-150 green"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['google_plus_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['google_plus_url']); ?>" target="_blank"
                    rel="noopener noreferrer">
                    <i class="ace-icon fa fa-google-plus-square bigger-150"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($cms_settings['rss_url'])): ?>
                <a href="<?= htmlspecialchars($cms_settings['rss_url']); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="ace-icon fa fa-rss-square orange bigger-150"></i>
                </a>
                <?php endif; ?>
            </span>
            &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
            <span class="bigger-120">
                <span class="blue bolder">(<?php echo htmlspecialchars($cms_settings['lang_code'] ?? ''); ?>)</span>
            </span>
        </div>

    </div>
</div>


<?= $this->render('foot'); ?>
<script>
// Get sidebar background color and apply it to footer
(function() {
    function applySidebarColorToFooter() {
        var sidebar = document.getElementById('sidebar');
        var footer = document.querySelector('.footer');

        if (sidebar && footer) {
            // Get computed background color of sidebar
            var sidebarStyle = window.getComputedStyle(sidebar);
            var sidebarBgColor = sidebarStyle.backgroundColor;

            // Also check if sidebar has a :before pseudo-element with background
            var sidebarBefore = window.getComputedStyle(sidebar, ':before');
            var sidebarBeforeBg = sidebarBefore.backgroundColor;

            // Use sidebar background, or sidebar:before background, or fallback
            var finalColor = sidebarBgColor;

            if (!finalColor || finalColor === 'rgba(0, 0, 0, 0)' || finalColor === 'transparent') {
                if (sidebarBeforeBg && sidebarBeforeBg !== 'rgba(0, 0, 0, 0)' && sidebarBeforeBg !==
                    'transparent') {
                    finalColor = sidebarBeforeBg;
                } else {
                    // Fallback: use common dark sidebar color
                    finalColor = '#222A2D';
                }
            }

            // Apply the color to footer
            footer.style.backgroundColor = finalColor;

            // Also apply to footer-inner to ensure it's visible
            var footerInner = footer.querySelector('.footer-inner');
            if (footerInner) {
                footerInner.style.backgroundColor = 'transparent';
            }
        }
    }

    // Apply on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applySidebarColorToFooter);
    } else {
        applySidebarColorToFooter();
    }

    // Also try after delays to ensure sidebar is fully rendered
    setTimeout(applySidebarColorToFooter, 100);
    setTimeout(applySidebarColorToFooter, 500);
    setTimeout(applySidebarColorToFooter, 1000);
})();
</script>
<script>
// Function to hide messages after 6 seconds
setTimeout(function() {
    var successMessage = document.getElementById('success-message');
    var errorMessage = document.getElementById('error-message');

    // Check if the success message exists and fade it out
    if (successMessage) {
        successMessage.style.transition = "opacity 0.5s ease";
        successMessage.style.opacity = 0; // Fade out effect
        setTimeout(function() {
            successMessage.style.display = 'none'; // Remove it from the layout
        }, 500); // Wait for fade out to complete
    }

    // Check if the error message exists and fade it out
    if (errorMessage) {
        errorMessage.style.transition = "opacity 0.5s ease";
        errorMessage.style.opacity = 0; // Fade out effect
        setTimeout(function() {
            errorMessage.style.display = 'none'; // Remove it from the layout
        }, 500); // Wait for fade out to complete
    }
}, 6000); // 6 seconds
</script>


<!-- basic scripts -->

<!--[if !IE]> -->
<script src="assets/js/jquery-2.1.4.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
<script type="text/javascript">
if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" +
    "<" + "/script>");
</script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- page specific plugin scripts -->

<!--[if lte IE 8]>
		  <script src="assets/js/excanvas.min.js"></script>
		<![endif]-->
<script src="assets/js/jquery-ui.custom.min.js"></script>
<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="assets/js/bootbox.js"></script>
<script src="assets/js/jquery.easypiechart.min.js"></script>
<script src="assets/js/jquery.gritter.min.js"></script>
<script src="assets/js/spin.js"></script>

<!-- ace scripts -->
<script src="assets/js/ace-elements.min.js"></script>
<script src="assets/js/ace.min.js"></script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
jQuery(function($) {
    /**
    $('#myTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      //console.log(e.target.getAttribute("href"));
    })
    	
    $('#accordion').on('shown.bs.collapse', function (e) {
    	//console.log($(e.target).is('#collapseTwo'))
    });
    */

    $('#myTab a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        //if($(e.target).attr('href') == "#home") doSomethingNow();
    })


    /**
    	//go to next tab, without user clicking
    	$('#myTab > .active').next().find('> a').trigger('click');
    */


    $('#accordion-style').on('click', function(ev) {
        var target = $('input', ev.target);
        var which = parseInt(target.val());
        if (which == 2) $('#accordion').addClass('accordion-style2');
        else $('#accordion').removeClass('accordion-style2');
    });

    //$('[href="#collapseTwo"]').trigger('click');


    $('.easy-pie-chart.percentage').each(function() {
        $(this).easyPieChart({
            barColor: $(this).data('color'),
            trackColor: '#EEEEEE',
            scaleColor: false,
            lineCap: 'butt',
            lineWidth: 8,
            animate: ace.vars['old_ie'] ? false : 1000,
            size: 75
        }).css('color', $(this).data('color'));
    });

    $('[data-rel=tooltip]').tooltip();
    $('[data-rel=popover]').popover({
        html: true
    });


    $('#gritter-regular').on(ace.click_event, function() {
        $.gritter.add({
            title: 'This is a regular notice!',
            text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" class="blue">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            image: 'assets/images/avatars/avatar1.png', //in Ace demo ./dist will be replaced by correct assets path
            sticky: false,
            time: '',
            class_name: (!$('#gritter-light').get(0).checked ? 'gritter-light' : '')
        });

        return false;
    });

    $('#gritter-sticky').on(ace.click_event, function() {
        var unique_id = $.gritter.add({
            title: 'This is a sticky notice!',
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" class="red">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            image: 'assets/images/avatars/avatar.png',
            sticky: true,
            time: '',
            class_name: 'gritter-info' + (!$('#gritter-light').get(0).checked ?
                ' gritter-light' : '')
        });

        return false;
    });


    $('#gritter-without-image').on(ace.click_event, function() {
        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'This is a notice without an image!',
            // (string | mandatory) the text inside the notification
            text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" class="orange">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            class_name: 'gritter-success' + (!$('#gritter-light').get(0).checked ?
                ' gritter-light' : '')
        });

        return false;
    });


    $('#gritter-max3').on(ace.click_event, function() {
        $.gritter.add({
            title: 'This is a notice with a max of 3 on screen at one time!',
            text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" class="green">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            image: 'assets/images/avatars/avatar3.png', //in Ace demo ./dist will be replaced by correct assets path
            sticky: false,
            before_open: function() {
                if ($('.gritter-item-wrapper').length >= 3) {
                    return false;
                }
            },
            class_name: 'gritter-warning' + (!$('#gritter-light').get(0).checked ?
                ' gritter-light' : '')
        });

        return false;
    });


    $('#gritter-center').on(ace.click_event, function() {
        $.gritter.add({
            title: 'This is a centered notification',
            text: 'Just add a "gritter-center" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
            class_name: 'gritter-info gritter-center' + (!$('#gritter-light').get(0).checked ?
                ' gritter-light' : '')
        });

        return false;
    });

    $('#gritter-error').on(ace.click_event, function() {
        $.gritter.add({
            title: 'This is a warning notification',
            text: 'Just add a "gritter-light" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
            class_name: 'gritter-error' + (!$('#gritter-light').get(0).checked ?
                ' gritter-light' : '')
        });

        return false;
    });


    $("#gritter-remove").on(ace.click_event, function() {
        $.gritter.removeAll();
        return false;
    });


    ///////


    $("#bootbox-regular").on(ace.click_event, function() {
        bootbox.prompt("What is your name?", function(result) {
            if (result === null) {

            } else {

            }
        });
    });

    $("#bootbox-confirm").on(ace.click_event, function() {
        bootbox.confirm("Are you sure?", function(result) {
            if (result) {
                //
            }
        });
    });

    /**
    	$("#bootbox-confirm").on(ace.click_event, function() {
    		bootbox.confirm({
    			message: "Are you sure?",
    			buttons: {
    			  confirm: {
    				 label: "OK",
    				 className: "btn-primary btn-sm",
    			  },
    			  cancel: {
    				 label: "Cancel",
    				 className: "btn-sm",
    			  }
    			},
    			callback: function(result) {
    				if(result) alert(1)
    			}
    		  }
    		);
    	});
    **/


    $("#bootbox-options").on(ace.click_event, function() {
        bootbox.dialog({
            message: "<span class='bigger-110'>I am a custom dialog with smaller buttons</span>",
            buttons: {
                "success": {
                    "label": "<i class='ace-icon fa fa-check'></i> Success!",
                    "className": "btn-sm btn-success",
                    "callback": function() {
                        //Example.show("great success");
                    }
                },
                "danger": {
                    "label": "Danger!",
                    "className": "btn-sm btn-danger",
                    "callback": function() {
                        //Example.show("uh oh, look out!");
                    }
                },
                "click": {
                    "label": "Click ME!",
                    "className": "btn-sm btn-primary",
                    "callback": function() {
                        //Example.show("Primary button");
                    }
                },
                "button": {
                    "label": "Just a button...",
                    "className": "btn-sm"
                }
            }
        });
    });



    $('#spinner-opts small').css({
        display: 'inline-block',
        width: '60px'
    })

    var slide_styles = ['', 'green', 'red', 'purple', 'orange', 'dark'];
    var ii = 0;
    $("#spinner-opts input[type=text]").each(function() {
        var $this = $(this);
        $this.hide().after('<span />');
        $this.next().addClass('ui-slider-small').
        addClass("inline ui-slider-" + slide_styles[ii++ % slide_styles.length]).
        css('width', '125px').slider({
            value: parseInt($this.val()),
            range: "min",
            animate: true,
            min: parseInt($this.attr('data-min')),
            max: parseInt($this.attr('data-max')),
            step: parseFloat($this.attr('data-step')) || 1,
            slide: function(event, ui) {
                $this.val(ui.value);
                spinner_update();
            }
        });
    });



    //CSS3 spinner
    $.fn.spin = function(opts) {
        this.each(function() {
            var $this = $(this),
                data = $this.data();

            if (data.spinner) {
                data.spinner.stop();
                delete data.spinner;
            }
            if (opts !== false) {
                data.spinner = new Spinner($.extend({
                    color: $this.css('color')
                }, opts)).spin(this);
            }
        });
        return this;
    };

    function spinner_update() {
        var opts = {};
        $('#spinner-opts input[type=text]').each(function() {
            opts[this.name] = parseFloat(this.value);
        });
        opts['left'] = 'auto';
        $('#spinner-preview').spin(opts);
    }



    $('#id-pills-stacked').removeAttr('checked').on('click', function() {
        $('.nav-pills').toggleClass('nav-stacked');
    });






    ///////////
    $(document).one('ajaxloadstart.page', function(e) {
        $.gritter.removeAll();
        $('.modal').modal('hide');
    });

});
</script>