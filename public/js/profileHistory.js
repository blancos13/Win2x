$(document).ready(function() {
    var isVisible = false,
		hideAllPopovers = function() {
			$('.popover').each(function() {
				$(this).popover('hide');
			});  
		};
	$(document).on('click', function(e) {
		hideAllPopovers();
        isVisible = false;
	});
	$('[data-toggle="popover-info"]').popover({
		html: true, 
		content: function() {
			return '<div class="popover-tip-text">'+ $(this).data('contenthtml') +'</div>';
		}
	}).on('click', function(e) {
        if(isVisible) hideAllPopovers();
        $(this).popover('show');
        $('.popover').off('click').on('click', function(e) {
            e.stopPropagation();
        });
        isVisible = true;
        e.stopPropagation();
    });
	$(document).on('click', '.history_nav .btn', function() {
		var tab = $(this).data('tab');
		$('.popover').popover('hide');
		$('.history_nav .btn').removeClass('isActive');
		$(this).addClass('isActive');
		$('.history_wrapper').hide();
		$('.history_wrapper.'+tab).show();
	});
	$(document).on('click', '.history_button', function() {
		var id = $(this).data('id');
		$.ajax({
			url: '/withdraw/cancel',
			type: 'post',
			data: {
				id: id
			},
			success: function(data) {
				if(data.success) {
					$('.withStatus_'+data.id).html('Canceled');
				}
				$.notify({
					type: data.type,
					message: data.msg
				});
			}
		});
	});
});