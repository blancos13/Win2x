$(document).ready(function() {
	let canvas = document.getElementById('crashChart'),
		ctx    = canvas.getContext('2d')
	
    this.socket = io.connect(':7777');
	
	Chart.pluginService.register({
		afterDraw: function(chart) {
			var ctx2 = chart,
				max = ctx2.chartArea.left-5,
				width = ctx2.width,
				height = ctx2.height - 10;
			ctx.save(),
			ctx.globalCompositeOperation = "destination-over";
			var lr = Math.round((width - 6) / 83.5) + 1,
				td = Math.round((height - 1) / 82.5) + 1;
			ctx.lineWidth = .5,
			ctx.strokeStyle = "rgba(255, 255, 255, 0.05)";
			for (var s = 0; s < lr; s++) {
				var c = max + 6 + 83 * s;
				ctx.beginPath(),
				ctx.setLineDash([4, 3]),
				0 === s && ctx.setLineDash([]),
				ctx.moveTo(c, 0),
				ctx.lineTo(c, height),
				ctx.stroke(),
				ctx.closePath()
			}
			for (var u = 0; u < td; u++) {
				var h = height - (88.8 * u + (u + 1 === td ? 1 : 0)),
					l = width - 6 - .5 - 9;
				ctx.beginPath(),
				ctx.setLineDash([4, 3]),
				0 === u && ctx.setLineDash([]),
				ctx.moveTo(max + 6, h),
				ctx.lineTo(l + max, h),
				ctx.stroke(),
				ctx.closePath()
			}
			ctx.globalCompositeOperation = "source-over",
			ctx.restore()
		}
	});
	
	let shadowLine = Chart.controllers.line.extend({
		initialize: function () {
			Chart.controllers.line.prototype.initialize.apply(this, arguments)
			
			var ctx = this.chart.ctx
			var originalStroke = ctx.stroke
			ctx.stroke = function () {
				ctx.save()
				ctx.shadowColor = 'rgba(0,0,0,0.3)'
				ctx.shadowOffsetX = 4
				ctx.shadowOffsetY = 4
				ctx.shadowBlur = 15
				originalStroke.apply(this, arguments)
				ctx.restore()
			}
		}
	})
	Chart.controllers.shadowLine = shadowLine
	
	let chart = new Chart(ctx, {
		type: 'shadowLine',
		data: {
			labels: [0],
			datasets: [{
				label: '',
				backgroundColor: 'rgba(73, 134, 245, 0.65)',
				borderColor: '#fff',
				pointRadius: 0,
				borderWidth: 8,
				data: [0],
			}]
		},
		options: {
			animation: false,
			title: {
				display: false
			},
			legend: {
				display: false,
			},
			layout: {
				padding: {
					left: 7
				}
			},
			scales: {
				xAxes: [{
					gridLines: {
						display: false,
					},
					ticks: {
						min: 1,
						stepSize: 1,
						display: false,
					}
				}],
				yAxes: [{
					gridLines: {
						display: false,
					},
					ticks: {
						beginAtZero:true,
						padding: 10,
						min: 1,
						max: 2,
						stepSize: 0.3,
						fontSize: 15,
						fontStyle: 600,
						fontFamily: "'Open Sans', sans-serif",
						fontColor: '#828f9a',
						callback: function(value, index, values) {
							if(value != '' && value.toFixed(1) == 1) return 0;
							if(!(index % parseInt(values.length / 5))) {
								return 'x' + value.toFixed(1);
						  	}
						}
					}
				}]
			}
		}
	})
	
    this.resetPlot = () => {
		chart.data.labels = [0];
		chart.data.datasets[0].data = [0];
		chart.options.scales.yAxes[0].ticks.max = 2;
		chart.data.datasets[0].backgroundColor = 'rgba(73, 134, 245, 0.65)';
		chart.update();
    }

    this.socket.on('crash', async res => {
        if(res.type == 'bet') this.publishBet(res);
        if(res.type == 'timer') this.publishTime(res);
        if(res.type == 'slider') this.parseSlider(res);
        if(res.type == 'game') this.reset(res);
    });

    this.publishTime = (res) => {
        $('#chartInfo').text('To start ' + res.value + 'sec.');
    }

    this.publishBet = (res) => {
        let html = '';
        for(var i in res.bets)
        {
            let bet = res.bets[i];
            html += '<tr><td class="username"><button type="button" class="btn btn-link" data-id="'+ bet.user.unique_id +'"><span class="sanitize-user"><div class="sanitize-avatar"><img src="'+ bet.user.avatar +'" alt=""></div><span class="sanitize-name">'+ bet.user.username +'</span></span></button></td><td><div class="bet-number"><span class="bet-wrap"><span>'+ bet.price +'</span><svg class="icon icon-coin '+ bet.balType +'"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span></div></td><td>х'+ bet.withdraw +'</td><td>';
            if(bet.status == 1) html += '<span class="bet-wrap win"><span>'+ bet.won +'</span><svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></span>';
            if(bet.status == 0) html += '<span class="bet-wrap wait"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-time"></use></svg></span>';
            html += '</td></tr>';
        }
        $('#bets').html(html);
    }

    this.reset = (res) => {
        $('#bets').html('');
        $('#chartInfo').css('color', '#ffc645').text('Loading');
        this.resetButton(false);
        this.resetPlot();
		$('#gameId').text(res.id);
        $('.hash .text').text(res.hash);
		$('.game-history .item').addClass('isAnimate');
        let html = '';
        for(var i in res.history) html += '<div class="item isAnimate checkGame" data-hash="'+res.history[i].hash+'"><div class="item-bet" style="color: '+res.history[i].color+';">x'+res.history[i].multiplier+'</div></div>';
		if($('.game-history .item').length >= 10) $('.game-history .item:nth-child(10)').remove();
        $('.game-history').html(html);
		setTimeout(function() {
			$('.game-history .item').removeClass('isAnimate');
		}, 1000);
    }

    this.parseSlider = (res) => {
		chart.data.labels = res.label;
		chart.data.datasets[0].data = res.data;
		chart.options.scales.yAxes[0].ticks.max = Math.max.apply(2, res.data) + 1;
		chart.update();
        
        $('#chartInfo').text(((res.crashed) ? 'Stop on ' : '') + 'x' + res.float.toFixed(2));
        if(res.crashed) 
        {
			chart.data.datasets[0].backgroundColor = 'rgba(167, 76, 92, 0.65)';
			chart.update();
            $('#chartInfo').css({
                'transition' : 'color 200ms ease',
                'color' : '#a74c5c'
            });
            $('.btn-withdraw span').text('Withdraw');
        } else {
            if(!window.isCashout && window.withdraw > 0) $('.btn-withdraw span').text('Withdraw ' + parseFloat(window.bet*parseFloat(res.float.toFixed(2))).toFixed(2));
            if(res.float >= window.withdraw && !window.isCashout) 
            {
                window.isCashout = true;
                $('.btn-withdraw').click();
            }
            $('#chartInfo').css({
                'transition' : 'color 200ms ease',
                'color' : res.color
            });
        }
    }
    
    this.notify = (r) => {
        $.notify({
            position : 'bottom-left',
            type: (r.success) ? 'success' : 'error',
            message: r.msg
        });
    }

    this.resetButton = result => {
        if(result) {
            $('.btn-play').hide();
            $('.btn-withdraw').show();
        } else {
            $('.btn-withdraw').hide();
            $('.btn-play').show();
        }
    }

    $('.btn-play').click(() => {
        $.ajax({
            url : '/crash/newBet',
            type : 'post',
            data : {
                bet : parseFloat($('#sum').val()) || 0,
                withdraw : parseFloat($('#betout').val()) || 0,
                balType: localStorage.getItem('balance')
            },
            success : res => {
                this.notify(res);
                if(res.success) 
                {
                    window.bet = res.bet;
                    $('.btn-withdraw span').text('Withdraw ' + (window.bet).toFixed(2));
                    this.resetButton(true);
                    window.withdraw = parseFloat($('#betout').val()) || 0;
                    window.isCashout = false;
                }
            }, 
            error : err => {
                console.log('New error');
                console.log(err.responseText);
            }
        });
    });

    $('.btn-withdraw').click(() => {
        window.isCashout = true;
        $.ajax({
            url : '/crash/cashout',
            type : 'post',
            success : res => {
                this.notify(res);
                if(res.success) 
                {
                    this.resetButton(false);
                    $('.btn-withdraw span').text('Withdraw');
                }
            },
            error : err => {
                this.notify({
                    success : false,
                    msg : 'Something went wrong...'
                });
                console.log(err.responseText);
            }
        })
    });

    $('a[data-method="last"]').click(() => {
        $.ajax({
            url : '/crash/last',
            type : 'post',
            success : b => $('#sum').val(b),
            error : e => $('#sum').val(0)
        });
    });

	$('#betout').on('input change', function() {
		$('.input-suffix span').text($(this).val());
	});
});

function getXAxisLabel(value) {
    try {
        var xMin = chart.options.scales.yAxes[0].ticks.min;
    } catch(e) {
        var xMin = undefined;
    }
    if (xMin === value) {
        return '';
    } else {
		chart.data.labels = value;
		chart.update();
    }
}