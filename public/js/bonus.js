$(document).ready(function() {
	var time,
		bonus = null,
		countdown;
    
    $('.list_list .list_item').click(function(e) {
        bonus = localStorage.setItem('bonus', $(this).attr('data-bonus'));
		$('#fortuneWheel').css('transition', 'transform 0s');
		$('#fortuneWheel').css('transform', 'rotate(0deg)');
        $('.list_list .list_item').removeClass('list_active');
		$('.popover').popover('hide');
        $(this).addClass('list_active');

        getCurrentBonus();

        e.preventDefault();
    });

    function getCurrentBonus() {
        bonus = localStorage.getItem('bonus') || 'group';
        $('.list_list .list_item.' + bonus).addClass('list_active');
        BonusInit(bonus);
    }

    getCurrentBonus();
	
	$('#purposeTip').popover({
        html : true,
		placement: 'top',
        content: '<div class="popover-tip-text">Active referral is the one who filled up the balance from 5$</div>'
    });
	
	$('body').on('click', function (e) {
		$('#purposeTip').each(function() {
			if(!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				$(this).popover('hide');
			}
		});
	});
	
	$(document).on('click', '#submitBonus', function() {
		$.ajax({
			url: '/free/spin',
			type: 'post',
			data: {
				type: localStorage.getItem('bonus') || 'group',
				recapcha: $('#captchaModal .g-recaptcha-response').val()
			},
			success: function(data) {
                grecaptcha.reset();
				if(data.success) {
					$('#captchaModal').modal('hide');
					if(data.bonusType == 'group') {
						time = Math.floor(new Date().getTime()/1000);
						var remaining = data.remaining - time;
						timer(remaining);
					}
					if(data.bonusType == 'refs') {
						$('.form_wrapper.refs .form_block').html('<div class="form_recharge">You have received this bonus!</div>');
					}
				}
				$.notify({
					type: data.type,
					message: data.msg
				});
			}
		})
	});
	
	function BonusInit(type) {
        $.ajax({
            url: '/free/getWheel',
            type: 'post',
			data: {
				type: type
			},
            success: r => {
				render(r.data);
				$('.form_wrapper').hide();
				$('.form_wrapper.'+r.type).show();
				time = Math.floor(new Date().getTime()/1000);
				var remaining = r.remaining - time;
				if(check) timer(remaining);
            },
            error: e => console.log(e.responseText)
        });
    }
	
	function render(data) {
		d3.select('#fortuneWheel').selectAll('svg').remove();
		var padding = { top: 0, right: 0, bottom: 0, left: 0 },
		w = 320,
		h = 320,
		r = Math.min(w, h) / 2,
		sw = 0;

		var svg = d3.select('#fortuneWheel')
			.append("svg")
			.data([data])
			.attr("width", w + padding.left + padding.right + 2 * sw)
			.attr("height", h + padding.top + padding.bottom + 2 * sw)
			.attr("viewBox", "0 0 "+ w +" "+ h +"");
		var container = svg.append("g")
			.attr("transform", "translate(" + (w / 2 + sw + padding.left) + "," + (h / 2 + sw + padding.top) + ")");
		var vis = container.append("g");
		var pie = d3.layout.pie().sort(null).value(function (d) { return 1; });
		var arc = d3.svg.arc().outerRadius(r-sw/2);
		var arcs = vis.selectAll("g.slice")
			.data(pie)
			.enter()
			.append("g")
			.attr("class", "slice");

		arcs.append("path")
		.attr("fill", function(d, i) {
			return data[i].bgColor
		})
		.attr("d", function (d) { return arc(d); });

		// add the text
		arcs.append("text").attr("transform", function (d) {
			d.innerRadius = 0;
			d.outerRadius = r;
			d.angle = (d.startAngle + d.endAngle) / 2;
			return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")translate(" + (d.outerRadius - 50 + 10) + " 6)";
		})
		.attr("fill", "#fff")
		.attr("font-size", 16)
		.attr("font-weight", 600)
		.attr("text-anchor", "end")
		.text(function (d, i) {
			return data[i].sum;
		});

		arcs.append("use").attr("transform", function(d) {
			d.innerRadius = 0;
			d.outerRadius = r;
			d.angle = (d.startAngle + d.endAngle) / 2;
			return "rotate(" + (180 * d.angle / Math.PI - 90) + ")translate(" + (d.outerRadius - 50 + 20) + " -9)";
		})
		.attr("width", 16)
		.attr("height", 16)
		.attr("xlink:href", "/img/symbols.svg#icon-coin")
		.attr("fill", function(d, i) {
			return data[i].iconColor
		});
	}
	
	function timer(remaining) {
		if(!remaining) {
			clearInterval(countdown);
			$('.form_wrapper.group .form_block').html('<div class="form_recharge"><span>Recharge through:</span><div class="form_timeLeft">00:00:00</div></div>');
			return;
		}
		clearInterval(countdown);
		countdown = setInterval(() => {
			if(remaining == 0) {
				remaining == null;
				clearInterval(countdown);
				$('.form_wrapper.group .form_block').html('<div class="form_value">'+ settings.bonus_group_time +' mins<div class="form_text">recharge</div></div><span id="spin-wheel-button" class=""><button type="button" class="btn" data-toggle="modal" data-target="#captchaModal">Spin</button></span>');
				return;
			}
			remaining--;
			var coul = convertTimestamp(remaining);
			$('.form_wrapper.group .form_block').html('<div class="form_recharge"><span>Recharge through:</span><div class="form_timeLeft">'+ coul +'</div></div>');
		}, 1*1000);
	}
	
	function convertTimestamp(s) {
		var hh = ((s - s % 3600) / 3600) % 60,
			min = ((s - s % 60) / 60) % 60,
			sec = s % 60,
			times;

		if(hh < 10) hh = '0' + hh;
		if(min < 10) min = '0' + min;
		if(sec < 10) sec = '0' + sec;
		
		if(s <= 0) return times = '00:00:00';
		times = hh + ':' + min + ':' + sec;
		return times;
	}
	
	var socket = io.connect(':7777');
	
	socket.on('bonus', function(data) {
		if(USER_ID == data.unique_id) {
			$('#fortuneWheel').css('transition', 'transform 4s cubic-bezier(0.15, 0.15, 0, 1)');
			$('#fortuneWheel').css('transform', 'rotate(-' + data.rotate + 'deg)');
		}
	});
});
	
function recaptchaCallback() {
	$('#submitBonus').removeAttr('disabled');
}