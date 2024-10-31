jQuery(document).ready(function() {

var pxp_overlay = '<div id="pxp-overlay"></div><div id="pxp-control-panel"><a href="#" id="pxp-control-button">Control Button</a><div id="pxp-slider"><div id="pxp-slider-value"></div></div></div>';
var pxp_image_src = jQuery('#pxp-image').attr('src');
var pxp_image_height = jQuery('#pxp-image').attr('height');
var pxp_image_opacity = jQuery('#pxp-image').attr('opacity');
var pxp_image_topspacing = jQuery('#pxp-image').attr('topspacing');
if(pxp_image_topspacing==1){topspacing = 28;} else {topspacing = 0;}

// manage overlay
jQuery('#pxp-image').hide();
jQuery('body').append(pxp_overlay);
jQuery('#pxp-overlay').css({ 'z-index' : 999, width: '100%', height : pxp_image_height + 'px', opacity : pxp_image_opacity/100, background: '#fff url('+ pxp_image_src +') center top no-repeat', position: 'absolute', left: 0, top: topspacing});

// manage control button
jQuery("#pxp-control-button").toggle(function(event) {
event.preventDefault();
  jQuery('#pxp-overlay').fadeOut(400);
  jQuery('#pxp-overlay').css({opacity : 0});
  jQuery('#pxp-control-button').addClass('off');
}, function() {
  jQuery('#pxp-overlay').fadeIn(400);
    jQuery('#pxp-overlay').css({opacity : pxp_image_opacity/100});
    jQuery('#pxp-control-button').removeClass('off');
});

// manage slider

    jQuery("#pxp-slider").slider({
        value: 30,
        min: 10,
        max: 100,
        step: 10,
        slide: function (event, ui) {
            jQuery("#pxp-slider-value").html(ui.value);
			jQuery('#pxp-overlay').css({opacity : ui.value/100});
        }
    });

    jQuery("#pxp-slider-value").html(jQuery('#pxp-slider').slider('value'));


});