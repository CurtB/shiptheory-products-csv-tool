$(function() {
    // resize results to browser viewport height as minimum
	var height = $(window).height() - 55;
    $('.results .container').css('min-height', height);
    
    // tooltip hover behaviour
    $('#tooltip').hover(function(event) {
        var position = $(this).offset();
        var left = (position.left + 21) + 'px';
        var top = (position.top - 60) + 'px';
        $('#tooltip-info').css({
            'display': 'block',
            'left': left,
            'top': top,
        });
    }, function() {
    	 $('#tooltip-info').css('display', 'none');
    });
    
    // control visibility of autofill inputs
    $('#auto_fill').on('change', autofillVisibility);
    $(document).ready(function() {
        autofillVisibility();
    });
});


function autofillVisibility() {
	if($('#auto_fill').is(':checked')) {
		$('.auto_fill_inputs').show();
		$('.auto_fill_inputs input').each(function() {
			$(this).attr('required', true);
		});
	} else {
		$('.auto_fill_inputs').hide();
		$('.auto_fill_inputs input').each(function() {
			$(this).attr('required', false);
		});
	}
}