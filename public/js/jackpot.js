$(document).ready(function() {
	var canvas = document.getElementById('circle').getContext('2d');
    canvas.canvas.width = 100;
    canvas.canvas.height = 100;

    var room = null,
		cldn = 0,
		spin = 0,
		cords = 0,
    	rotate = [];
    
    $('.rooms .btn').click(function(e) {
        room = localStorage.setItem('room', $(this).attr('data-room'));

        $('.rooms .btn').removeClass('isActive');
        $(this).addClass('isActive');

        getCurrentRoom();

        e.preventDefault();
    });

    function getCurrentRoom() {
        room = localStorage.getItem('room') || 'easy';
        $('.rooms .btn.' + room).addClass('isActive');
        JackpotInit(room);
		if(typeof rotate[room] == 'undefined') {
			rotate[room] = {spin: 0, time: 0};
		}
    }

    getCurrentRoom();

    const socket = io.connect(':7777');

    socket.on('jackpot', r => {
        if(r.type == 'timer' && room == r.room) {
			var sec = r.data.sec,
				min = r.data.min;
			if(sec < 10) sec = '0' + sec;
			if(min < 10) min = '0' + min;
			$('#timer').text(min + ':' + sec);
		}
        if(r.type == 'update' && r.data.success && room == r.room) JackpotParse(r.data.data);
        if(r.type == 'slider' && room == r.room) {
			cldn = 0;
			cords = r.data.cords;
			let timer = setInterval(() => {
				if(cldn >= 6) {
					rotate[r.room] = {spin: cords, time: 0};
					clearInterval(timer);
					return;
				}
				cldn++;
				spin = (cords/6)*cldn;
				rotate[r.room] = {spin: spin, time: (6-cldn)*1000};
			}, 1*1000)
			$('.spinner').css({
				transition: 'all 6000ms ease',
				transform: 'rotate('+ r.data.cords +'deg)'
			});
			
			setTimeout(function () {
				var wbl = '';
				if(r.data.winner_balance > 0) wbl += '<div class="payout">'+ r.data.winner_balance +' <svg class="icon icon-coin balance"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>';
				var wbn = '';
				if(r.data.winner_bonus > 0) wbn += '<div class="payout">'+ r.data.winner_bonus +' <svg class="icon icon-coin bonus"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>';
				
				var html = '';
					html += '<div class="game-tooltip isTransparent won '+ r.room +'"><div class="wrap"><div class="user"><button type="button" class="btn btn-link" data-id="'+ r.data.winner_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ r.data.winner_avatar +'" alt=""></div><span class="sanitize-name">'+ r.data.winner_name +'</span></span></button></div>'+ wbl + wbn +'<div class="badge"><div class="text">Победитель</div></div><div class="status"><span>Lucky ticket <span class="profit">'+ r.data.ticket +'</span> <svg class="icon"><use xlink:href="/img/symbols.svg#icon-ticket"></use></svg></span></div></div></div>';
				$('.game-area-content').append(html);
				setTimeout(function () { $('.game-tooltip.won.'+r.room).addClass('isActive'); }, 1000);
			}, 6200);
		}
        if(r.type == 'newGame') {
			if(room == r.room) {
				$('.spinner').css({
					transition: 'all 1500ms ease',
					transform: 'rotate(0deg)'
				});
				$('#bets').html('');
				$('#chances').html('');
				$('#value').text('0.00');
        		$('#gameId').text(r.data.data.game.game_id);
				$('#hash').text(r.data.data.game.hash);
				var min = Math.floor(r.data.data.time/60),
					sec = r.data.data.time-(Math.floor(r.data.data.time/60)*60);
				if(sec < 10) sec = '0' + sec;
				if(min < 10) min = '0' + min;
				$('#timer').text(min + ':' + sec);

				chart.options.plugins.labels.images = [];
				chart.data.datasets[0].data = [100];
				chart.data.datasets[0].backgroundColor = ['rgba(255, 255, 255, 0.05)'];
				chart.update();
			}
			rotate[r.room] = {spin: 0, time: 0};
			$('.game-tooltip.won').removeClass('isActive');
			setTimeout(function () { $('.game-tooltip.won.'+r.room).remove(); }, 2000);
		}
    });

    function JackpotParse(res) {
        let data = [], avatars = [], colors = [];

        let bets = '';
        res.bets.forEach(bet => {
            bets += '<tr><td class="username"> <button type="button" class="btn btn-link" data-id="'+bet.user.id+'"> <span class="sanitize-user"><div class="sanitize-avatar"><img src="'+bet.user.avatar+'" alt="" style="border: 1px solid #'+bet.bet.color+';"></div> <span class="sanitize-name">'+bet.user.username+'</span> </span> </button></td><td><div class="bet-number"> <span class="bet-wrap"> <span>'+bet.bet.amount+'</span> <svg class="icon icon-coin '+bet.bet.balance+'"> <use xlink:href="/img/symbols.svg#icon-coin"></use> </svg> </span></div></td><td>'+bet.bet.chance+'%</td><td><div class="bet-number rtl"> <span class="bet-wrap"> <span>'+bet.bet.from+' - '+bet.bet.to+'</span> <svg class="icon"> <use xlink:href="/img/symbols.svg#icon-ticket"></use> </svg> </span></div></td></tr>';
        });
        $('#bets').html(bets);

        let chances = '';
        res.chances.forEach(chance => {
            chances += '<div class="item" data-toggle="tooltip" data-placement="top" title="'+chance.user.username+'"><div class="user"><img src="'+chance.user.avatar+'" alt="" style="border: 1px solid #'+chance.color+';"></div><div class="hit">'+chance.chance+'%</div></div>';

            data.push(parseFloat(chance.chance));
            colors.push('#' + chance.color);
            avatars.push((parseFloat(chance.chance) >= 5) ? {
                src: chance.user.avatar,
                width: 35,
                height: 35
            } : {
                src: '/img/blank.png',
                width: 0,
                height: 0
            });
        });
        $('#chances').html(chances);
		
		$('.spinner').css({
			transition: 'all '+ rotate[res.room].time +'ms ease',
			transform: 'rotate('+ rotate[res.room].spin +'deg)'
		});

        $('#value').text(res.amount.toFixed(2));
        $('#hash').text(res.hash);
        $('#minBet').text(res.min);
        $('#maxBet').text(res.max);
        $('#gameId').text(res.game_id);
		
		let min = Math.floor(res.time/60),
			sec = res.time-(Math.floor(res.time/60)*60);
		if(sec < 10) sec = '0' + sec;
		if(min < 10) min = '0' + min;
		if(res.bets.length <= 2) $('#timer').text(min + ':' + sec);
		
		if($('.game-tooltip.won').length != 0) {
			$('.game-tooltip.won').removeClass('isActive');
			$('.game-tooltip.won.'+res.room).addClass('isActive');
		}
		
		chart.options.plugins.labels.images = avatars;
		if(data.length == 0) {
			chart.data.datasets[0].data = [100];
			chart.data.datasets[0].backgroundColor = ['rgba(255, 255, 255, 0.05)'];
			chart.update();
		} else {
			chart.data.datasets[0].data = data;
			chart.data.datasets[0].backgroundColor = colors;
			chart.update();
		}
    }

    $('.btn-play').click(function() {
        $.ajax({
            url: '/jackpot/newBet',
            type: 'post',
            data: {
                amount: parseFloat($('#sum').val()),
                balance: localStorage.getItem('balance') || 'balance',
                room: room
            },
            success: r => nAlert(r),
            error: e => console.log(e.responseText)
        });
    });

    $('.chances').kinetic({
        filterTarget: function(target, e) {
            if (!/down|start/.test(e.type)){
                return !(/area|a|input/i.test(target.tagName));
            }
        }
    });
	$('.btn-next').click(function() {
		 $('.chances').kinetic('start', { velocity: 5 });
	});
	$('.btn-prev').click(function() {
		 $('.chances').kinetic('start', { velocity: -5 });
    });


    function JackpotInit() {
        $.ajax({
            url: '/jackpot/init',
            type: 'post',
            data: {
                room: room
            },
            success: r => {
                if(r.success) JackpotParse(r.data);
            },
            error: e => console.log(e.responseText)
        });
    }
	
    window.chart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: '',
                data: [1],
                backgroundColor: ['rgba(255, 255, 255, 0.05)'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: 1,
            cutoutPercentage: 65,
            legend: {
                display: 0
            },
            tooltips: {
                enabled: 0
            },
            hover: {
                mode: null
            },
            plugins: {
                labels: {
                    render: 'image',
                    images: [],
                },
            }
        }
    });

    function nAlert(message, type) {
        if(typeof message == 'object') {
            type = (message.success) ? 'success' : 'error';
            message = message.msg;
        }

        $.notify({
            type: type,
            message: message
        });
    }
});