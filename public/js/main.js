$(document).ready(function() {
	initBalance();
	const container = document.querySelector('.chat-conversation-inner');
	const ps = new PerfectScrollbar('.chat-conversation-inner', {
		useBothWheelAxes: true
	});
	new PerfectScrollbar('.tos-modal-block', {
		useBothWheelAxes: true
	});
	$(window).resize(function() {
		container.scrollTop = container.scrollHeight - container.clientHeight;
		ps.update();
		if($(window).width() > 820) {
			$('body').removeClass('hidden');
			$('.right-sidebar').removeClass('opened');
			$('.right-sidebar .user-profile').removeClass('current');
			$('.right-sidebar .chat').addClass('current');
		}
    }).trigger('resize');
	$(window).scroll(function() {
		if($(window).width() > 820) {
			if($(this).scrollTop() > 0) $('.header, .right-sidebar').addClass('sticky');          
			else $('.header, .right-sidebar').removeClass('sticky');
		} else {
			$('.header, .right-sidebar').removeClass('sticky');
		}
	}).trigger('scroll');
	$(document).tooltip({
		selector: '[data-toggle="tooltip"]'
	});
	$(document).on('click', '.top-nav .toggle .btn', function(e) {
		e.preventDefault();
		var $this = $(this).parent(),
			$content = $this.find('ul');
		if(!$content.hasClass('isOpen')) {
			$(this).addClass('isOpen');
			$content.addClass('isOpen');
			setTimeout(function () {
				$(document).on('click.headerToggle', function (e) {
					if(!$(e.target).closest($content).length) {
						$this.removeClass('isOpen');
						$content.removeClass('isOpen');
						$(document).off('click.headerToggle');
					}
				});
			}, 10);
		}
	});
	$(document).on('click', '.deposit-wrap .dropdown-item', function() {
		var type;
		if($(this).data('id') == 'balance') {
			$('.deposit-wrap .dropdown-toggle .selected').removeClass('bonus');
			$('.deposit-wrap .dropdown-toggle .selected').addClass('balance');
			$('#exchange').remove();
			$('#wallet').remove();
			$('.deposit-block').append('<button type="button" class="btn" id="wallet">Wallet</button>');
			type = 'balance';
		} else {
			$('.deposit-wrap .dropdown-toggle .selected').addClass('bonus');
			$('.deposit-wrap .dropdown-toggle .selected').removeClass('balance');
			$('#exchange').remove();
			$('#wallet').remove();
			$('.deposit-block').append('<button type="button" class="btn btn-light-gray" id="exchange">Exchange</button>');
			type = 'bonus';
		}
		localStorage.setItem('balance', type);
		var value = $('.balance-item.' + type + ' #'+ type +'_bal').text();
		$('.deposit-block #balance').text(value);
	});
	var popoverTemplate = '<div class="popover smile-popover" role="tooltip"><div class="popover-inner"><div class="popover-body"></div></div><span class="arrow" style="left: 165px;"></span></div>';

    var content = '<div class="smiles-list"><span class="s s-1f60a" data-item="1f60a"></span><span class="s s-1f60b" data-item="1f60b"></span><span class="s s-1f60c" data-item="1f60c"></span><span class="s s-1f60d" data-item="1f60d"></span><span class="s s-1f60e" data-item="1f60e"></span><span class="s s-1f60f" data-item="1f60f"></span><span class="s s-1f61a" data-item="1f61a"></span><span class="s s-1f61b" data-item="1f61b"></span><span class="s s-1f61c" data-item="1f61c"></span><span class="s s-1f61d" data-item="1f61d"></span><span class="s s-1f61f" data-item="1f61f"></span><span class="s s-1f61e" data-item="1f61e"></span><span class="s s-1f62a" data-item="1f62a"></span><span class="s s-1f62b" data-item="1f62b"></span><span class="s s-1f62c" data-item="1f62c"></span><span class="s s-1f62d" data-item="1f62d"></span><span class="s s-1f62e" data-item="1f62e"></span><span class="s s-1f62f" data-item="1f62f"></span><span class="s s-1f92a" data-item="1f92a"></span><span class="s s-1f92b" data-item="1f92b"></span><span class="s s-1f92c" data-item="1f92c"></span><span class="s s-1f92e" data-item="1f92e"></span><span class="s s-1f92d" data-item="1f92d"></span><span class="s s-1f600" data-item="1f600"></span><span class="s s-1f601" data-item="1f601"></span><span class="s s-1f92f" data-item="1f92f"></span><span class="s s-1f603" data-item="1f603"></span><span class="s s-1f602" data-item="1f602"></span><span class="s s-1f604" data-item="1f604"></span><span class="s s-1f605" data-item="1f605"></span><span class="s s-1f606" data-item="1f606"></span><span class="s s-1f607" data-item="1f607"></span><span class="s s-1f608" data-item="1f608"></span><span class="s s-1f609" data-item="1f609"></span><span class="s s-1f610" data-item="1f610"></span><span class="s s-1f611" data-item="1f611"></span><span class="s s-1f612" data-item="1f612"></span><span class="s s-1f613" data-item="1f613"></span><span class="s s-1f614" data-item="1f614"></span><span class="s s-1f615" data-item="1f615"></span><span class="s s-1f618" data-item="1f618"></span><span class="s s-1f620" data-item="1f620"></span><span class="s s-1f622" data-item="1f622"></span><span class="s s-1f621" data-item="1f621"></span><span class="s s-1f623" data-item="1f623"></span><span class="s s-1f624" data-item="1f624"></span><span class="s s-1f625" data-item="1f625"></span><span class="s s-1f626" data-item="1f626"></span><span class="s s-1f628" data-item="1f628"></span><span class="s s-1f629" data-item="1f629"></span><span class="s s-1f630" data-item="1f630"></span><span class="s s-1f631" data-item="1f631"></span><span class="s s-1f627" data-item="1f627"></span><span class="s s-1f632" data-item="1f632"></span><span class="s s-1f633" data-item="1f633"></span><span class="s s-1f636" data-item="1f636"></span><span class="s s-1f643" data-item="1f643"></span><span class="s s-1f642" data-item="1f642"></span><span class="s s-1f641" data-item="1f641"></span><span class="s s-1f644" data-item="1f644"></span><span class="s s-1f910" data-item="1f910"></span><span class="s s-1f911" data-item="1f911"></span><span class="s s-1f914" data-item="1f914"></span><span class="s s-1f913" data-item="1f913"></span><span class="s s-1f915" data-item="1f915"></span><span class="s s-1f923" data-item="1f923"></span><span class="s s-1f925" data-item="1f925"></span><span class="s s-1f929" data-item="1f929"></span><span class="s s-1f928" data-item="1f928"></span><span class="s s-2764" data-item="2764"></span></div>';
	
	$(document).popover({
		container: 'body',
        html : true,
		selector: '[data-toggle="popover"]',
		template : popoverTemplate,
        content: content
	});
	
	$('body').on('click', function (e) {
		$('[data-toggle="popover"]').each(function() {
			if(!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				$(this).popover('hide');
			}
		});
	});
	
	$(document).on('click', '.smiles-list span', function() {
        $('.chat-editable').append('<img class="s s-' + $(this).data('item') + '" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"/>');
		$('[data-toggle="popover"]').popover('hide');
	});
	
	$(document).bind('enterKey', '.chat-editable', function (e) {
        var input = $('.chat-editable');
        var optional = $('#optional').val();
        var msg = input.html();
        if(msg != '') {
			$.ajax({
				url: '/chat',
				type: 'post',
				data: {
					messages: msg,
					optional: optional
				},
				success: function(data) {
					if(data) {
						if(data.status == 'success') {
							input.html('');
						} else {
							input.html('');
						}
						$.notify({
							type: data.status,
							message: data.message
						});
					} else input.html('');
				}
			})
        }
    });
	
	$(document).on('keypress', '.chat-editable', function(e) {
		var keyCode = e.which;
		if(keyCode == 13) {
			$(this).trigger('enterKey'); 
			e.preventDefault();
		}
	});
	
	$(document).on('click', '.chat-controls .sendMessage', function(e) {
		$('.chat-editable').trigger('enterKey'); 
		e.preventDefault();
	});
	
	$(document).on('click', '.tabs-nav .item', function(e) {
        if(!$(this).is('.current')) {
            $('.tabs-nav .item, .right-sidebar .tab').removeClass('current');
            $(this).addClass('current');
            $('.right-sidebar .tab:eq('+$(this).index()+')').addClass('current');
        }   
    });
	
	$(document).on('click', '.top-nav-wrapper .opener', function(e) {
        if(!$(this).is('.opened')) {
            $(this).addClass('opened');
            $('.top-nav').addClass('opened');
        } else {
			$(this).removeClass('opened');
            $('.top-nav').removeClass('opened');
		}
    });
	
	$(document).on('click', '#wallet', function() {
		$('#walletModal').modal('show');
	});
	
	$(document).on('click', '#exchange', function() {
		$('#exchangeModal').modal('show');
	});
	
	$(document).on('click', '#otherMenu', function() {
		if(!$('.pull-out.other').is('.opened')) {
            $('.pull-out.other').addClass('opened');
            $('.pull-out.game').removeClass('opened');
            $('.right-sidebar').removeClass('opened');
        } else {
            $('.pull-out.other').removeClass('opened');
		}
	});
	
	$(document).on('click', '#gamesMenu', function() {
		if(!$('.pull-out.game').is('.opened')) {
            $('.pull-out.game').addClass('opened');
            $('.pull-out.other').removeClass('opened');
            $('.right-sidebar').removeClass('opened');
        } else {
            $('.pull-out.game').removeClass('opened');
		}
	});
	
	$(document).on('click', '#chatMenu', function() {
		if(!$('.right-sidebar').is('.opened')) {
            $('body').addClass('hidden');
            $('.right-sidebar').addClass('opened');
            $('.right-sidebar .user-profile').removeClass('current');
            $('.right-sidebar .chat').addClass('current');
            $('.pull-out.other').removeClass('opened');
            $('.pull-out.game').removeClass('opened');
        } else {
            $('body').removeClass('hidden');
            $('.right-sidebar').removeClass('opened');
		}
	});
	
	$(document).on('click', '#profileMenu', function() {
		if(!$('.right-sidebar').is('.opened')) {
            $('body').addClass('hidden');
            $('.right-sidebar').addClass('opened');
            $('.right-sidebar .chat').removeClass('current');
            $('.right-sidebar .user-profile').addClass('current');
            $('.pull-out.other').removeClass('opened');
            $('.pull-out.game').removeClass('opened');
        } else {
            $('body').removeClass('hidden');
            $('.right-sidebar').removeClass('opened');
		}
	});
	
	$(document).on('click', '.pull-out .close-btn, .right-sidebar .close-btn', function() {
		if($('.pull-out, .right-sidebar').is('.opened')) {
            $('body').removeClass('hidden');
            $('.pull-out, .right-sidebar').removeClass('opened');
			$('body').removeClass('hidden');
			$('.right-sidebar').removeClass('opened');
			$('.right-sidebar .user-profile').removeClass('current');
			$('.right-sidebar .chat').addClass('current');
        }
	});
	
	$(document).on('click', '.user-link, .btn-link', function() {
		$.ajax({
            url : '/getUser',
            type : 'post',
            data : {
                id : $(this).data('id')
            },
            success : function(data) {
				if(data.success) {
					var html = '';
						html += '<div class="user-modal__head">';
						html += '<div class="avatar"><img src="'+ data.info['avatar'] +'" alt=""></div>';
						html += '<div class="user-block"><div class="user-name"><span class="sanitize-name"><span class="sanitize-text">'+ data.info['username'] +'</span></span></div></div>';
						html += '</div>';
						html += '<div class="card-stats">';
						html += '<div class="stats-item">';
						html += '<div class="item-label">Bet amount</div>';
						html += '<div class="item-value positive">';
						html += '<div class="icon-wrapper">';
						html += '<svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg>';
						html += '</div> '+ data.info['betAmount'] +'</div>';
						html += '</div>';
						html += '<div class="stats-item">';
						html += '<div class="item-label">Total games</div>';
						html += '<div class="item-value">'+ data.info['totalGames'] +'</div>';
						html += '</div>';
						html += '</div>';
						html += '<div class="card-stats">';
						html += '<div class="stats-item">';
						html += '<div class="item-label">Victories</div>';
						html += '<div class="item-value">'+ data.info['wins'] +'</div>';
						html += '</div>';
						html += '<div class="stats-item">';
						html += '<div class="item-label">Defeats</div>';
						html += '<div class="item-value">'+ data.info['lose'] +'</div>';
						html += '</div>';
						html += '</div>';
					$('#userModal .user-modal__container').html(html);
					$('#userModal').modal('show');
				} else {
					$.notify({
						type: data.type,
						message: data.msg
					});
					return false;
				}
            },
            error : function(data) {
                console.log(data.responseText);
            }
        });
	});
	
	$(document).on('click', '.select-payment .dropdown-menu .dropdown-item', function() {
		$('.tab.active .select-payment .dropdown-menu .dropdown-item').removeClass('active');
		$('.tab.active .select-payment .dropdown-toggle').html($(this).html() + '<div class="opener"><svg class="icon icon-down"><use xlink:href="/img/symbols.svg#icon-down"></use></svg></div>');
		$(this).addClass('active');
		var type = $('.deposit-modal-component .tab.active').data('type');
		var system = $(this).data('system');
		if(type == 'deposite') $('#depositType').val(system)
		else {
			$('#withdrawType').val(system);
			calcSum(system);
		}
	});
	
	$(document).on('click', '.deposit-modal-component .btn-tab', function(e) {
        if(!$(this).is('.isActive')) {
            $('.deposit-modal-component .tab').removeClass('active');
            $('.deposit-modal-component .btn-tab').removeClass('isActive');
            $(this).addClass('isActive');
            $('.deposit-modal-component .tab:eq('+$(this).index()+')').addClass('active');
        }   
    });
	
	$(document).on('click', '.bet-component .btn-action, .bet-component .btn-bet-clear', function() {
		let value = parseFloat($('#sum').val()) || 0,
            all = parseFloat($('#balance').text()),
            thisMethod = $(this).data('action'),
            thisValue = parseFloat($(this).data('value'));

        switch(thisMethod) {
            case 'plus' : 
                value += thisValue;
                break;
            case 'divide' :
                value = parseFloat((value/thisValue).toFixed(2));
                break;
            case 'clear' :
                value = 0;
                break;
            case 'last' : 
                value = localStorage.getItem('last');
                break;
            case 'all' : 
                value = all;
                break;
            case 'multiply' :
                value *= thisValue;
                break;
        }

        $('#sum').val(value.toFixed(2)).trigger('input');
    });
	
	$(document).on('change keydown paste input', '#exSum', function() {
		var sum = parseFloat($(this).val()) || 0;
		var total = sum/settings.exchange_curs;
		if(settings.exchange_min > sum) total = 0;
		$('#exTotal').text(Math.floor(total));
	});
	
	$(document).on('click', '.exchangeBonus', function() {
		var sum = parseFloat($('#exSum').val());
		$.ajax({
            url : '/exchange',
            type : 'post',
            data : {
                sum : sum
            },
			success : function(data) {
				if(data.type == 'success') {
					$('#exchangeModal').modal('hide');
					$('#exSum').val('')
					$('#exTotal').val(0)
				}
				$.notify({
                    position : 'top-right',
                    type: data.type,
                    message: data.msg
                });
		        return false;
			},
            error : function(data) {
                console.log(data.responseText);
            }
        });
	});
	
	$(document).on('submit', '#payment', function(e) {
        $.ajax({
            url     : $(this).attr('action'),
            type    : $(this).attr('method'),
            data    : $(this).serialize(),
            success : function(data) {
                if(data.success) {
					$('body').append(data.form);
					document.getElementById("coinPayForm").submit();
                } else {
					$.notify({
						type: data.type,
						message: data.msg
					});
                }
            },
            error   : function(err) {
                console.log(err.responseText);
				$.notify({
					type: 'error',
					message: 'Something went wrong...'
				});
            }
        })
        e.preventDefault(); 
    });
	
	$('#valwithdraw').on('change paste input', function() {
		calcSum($('#withdrawType').val());
	});
	
    $('#withdraw-checkbox').click(function() {
        $('#withdraw-checkbox').attr('checked', 'checked');
        if($(this).prop('checked') == true){
            $('#submitwithdraw').removeAttr('disabled');
        } else {
            $('#submitwithdraw').attr('disabled', 'false');
            $('#withdraw-checkbox').removeAttr('checked');
        }
    });
	
	function calcSum(system) {
		if(!$('#withdrawType').val()) return;
        if(system == 'perfectmoney') {
            var perc = parseFloat(settings.pm_fee);
            var min = parseFloat(settings.pm_min);
            var wallet = 'Uxxxxxxxx';
        } else {
            var perc = parseFloat(settings.coinpayments_fee);
            var min = parseFloat(settings.coinpayments_min);
            var wallet = 'xxxxxxxxxxxxxxxxxxxx';
        }
		$('.com-row span').html((perc != 0 ? perc + '%' : 'No'));
		var sum = parseFloat($('#valwithdraw').val());
        var comission = sum-(sum/100*perc);
        if(!sum) comission = 0;
        if(comission < 0) comission = 0;
		$('#numwallet').attr('placeholder', wallet);
		$('#totalwithdraw').text(parseFloat(comission).toFixed(2));
		$('#valwithdraw').attr('placeholder', 'Min. amount '+ min +'$.');
		getConv();
    }
	
	function getConv() {
		var sys = $('#withdrawType').val();
		var val = $('#valwithdraw').val() || 0;
		var cur;
		if(sys == 'perfectmoney') {
			$('#withdrawGive').html(val + '$');
		} else {
			val = parseFloat(val/btcrate).toFixed(8);
			cur = parseFloat(val/rates[sys]['rate']).toFixed(8);
			$('#withdrawGive').html(cur + ' ' + sys);
		}
	}
	
	$(document).on('click', '#submitwithdraw', function() {
		var system = $('#withdrawType').val();
		var value = $('#valwithdraw').val();
		var wallet = $('#numwallet').val();
		
		$.ajax({
            url : '/withdraw',
            type : 'post',
            data : {
                system : system,
                value : value,
				wallet : wallet,
				recapcha : $('#withForm .g-recaptcha-response').val()
            },
			success : function(data) {
				if(data.success) {
					$('#walletModal').modal('hide');
					$('#valwithdraw').val('');
					$('#numwallet').val('');
					$('#totalwithdraw').text('0');
					$('#submitwithdraw').attr('disabled', 'false');
				}
				$.notify({
                    type: data.type,
                    message: data.msg
                });
		        return false;
			},
            error : function(data) {
                console.log(data.responseText);
            }
        });
	});
	
	$(document).on('click', '.shareToChat', function() {
		$.ajax({
			url: '/chat',
			type: 'post',
			data: {
				messages : '$bal',
				balType: localStorage.getItem('balance')
			},
			success: function(data) {
				if(data) {
					$.notify({
						type: data.status,
						message: data.message
					});
				}
			}
		})
	});
	
	$(document).on('click', '.unbanMe', function() {
		$.ajax({
			url : '/unbanMe',
			type : 'post',
			success : function(data) {
				$.notify({
					position : 'top-right',
					type: data.type,
					message: data.msg
				});
				return false;
			},
			error : function(data) {
				console.log(data.responseText);
			}
		});
	});
	
	$(document).on('click', '.btnToggle button', function() {
		if(!$(this).is('.isActive')) {
            $('.btnToggle button').removeClass('isActive');
            $(this).addClass('isActive');
        } 
	});
	
    $(document).on('click', '.checkHash', function() {
        var hash = $('#gameHash').val();
        $.ajax({
            url : '/fair/check',
            type : 'post',
            data : {
                hash : hash
            },
            success : function(data) {
                if(data.success) {
                    $('#gameRound').text(data.round);
                    $('#gameNumber').text(data.number);
                    $('.fair-table').slideDown();
                } else {
                    $('#gameRound').text('');
                    $('#gameNumber').text('');
                    $('.fair-table').slideUp();
                }
                $.notify({
                    type: data.type,
                    message: data.msg
                });
                return false;
            },
            error : function(data) {
                console.log(data.responseText);
            }
        });
    });
	
    $(document).on('click', '#withdraw-button .btn', function() {
		if($(this).attr('disabled') == true) return;
        $.ajax({
            url : '/affiliate/get',
            type : 'post',
            success : function(data) {
                if(data.success) {
                    $('.affiliate-stats .right .num').text('0.00');
					$('#withdraw-button .btn').attr('disabled', true);
                }
                $.notify({
                    type: data.type,
                    message: data.msg
                });
                return false;
            },
            error : function(data) {
                console.log(data.responseText);
            }
        });
    });
	
    $(document).on('click', '.checkGame', function() {
		var hash = $(this).data('hash');
        $('#gameHash').val(hash);
		$('#fairModal').modal('show');
    });
	
	$(document).on('hidden.bs.modal', '#fairModal', function () {
        $('#gameHash').val('');
        $('#gameRound').text('');
        $('#gameNumber').text('');
		$('.fair-table').slideUp();
	});
	
    $(document).on('click', '.activatePromo', function() {
        var code = $('#promoInput').val();
        $.ajax({
            url : '/promo/activate',
            type : 'post',
            data : {
                code : code
            },
            success : function(data) {
				$('#promoModal').modal('hide');
				$('#promoInput').val('')
                $.notify({
                    type: data.type,
                    message: data.msg
                });
                return false;
            },
            error : function(data) {
                console.log(data.responseText);
            }
        });
    });
	
	var socket = io.connect(':7777');
	socket
	.on('online', function (data) {
		$('.chat-online span').text(data); 
	})
	.on('message', function(msg) {
        if(USER_ID == msg.unique_id) {
            $.notify({
                type: msg.type,
                message: msg.msg
            });
        }
    })
	.on('updateBalance', function(data) {
        if(USER_ID == data.unique_id) {
			if(localStorage.getItem('balance') == 'balance') $('.deposit-block #balance').text(data.balance.toFixed(2));
			$('.balance-item.balance .value').text(data.balance.toFixed(2));
		}
    })
	.on('updateBonus', function(data) {
        if(USER_ID == data.unique_id) {
			if(localStorage.getItem('balance') == 'bonus') $('.deposit-block #balance').text(data.bonus.toFixed(2));
			$('.balance-item.bonus .value').text(data.bonus.toFixed(2));
		}
    })
	.on('chat', function (data) {
		if(admin == 1 || moder == 1) {
            var panel = '';
			if(!data.ban) {
				panel += '<div class="delete">';
				panel += '<button type="button" class="btn btn-light" onclick="chatdelet('+ data.time2 +')">';
				panel += '<svg class="icon"><use xlink:href="/img/symbols.svg#icon-close"></use></svg><span>Delete</span>';
				panel += '</button>';
				if(!data.admin) {
					panel += '<button type="button" class="btn btn-light btnBan" data-name="' + data.username + '" data-id="' + data.unique_id + '">';
					panel += '<svg class="icon">';
					panel += '<use xlink:href="/img/symbols.svg#icon-ban"></use>';
					panel += '</svg><span>Ban</span></button>';
				}
				panel += '</div>';
			} else {
				panel += '<div class="delete">';
				panel += '<button type="button" class="btn btn-light" onclick="chatdelet('+ data.time2 +')">';
				panel += '<svg class="icon"><use xlink:href="/img/symbols.svg#icon-close"></use></svg><span>Delete</span>';
				panel += '</button>';
				if(!data.admin) {
					panel += '<button type="button" class="btn btn-light btnUnBan" data-name="' + data.username + '" data-id="' + data.unique_id + '">';
					panel += '<svg class="icon">';
					panel += '<use xlink:href="/img/symbols.svg#icon-ban"></use>';
					panel += '</svg><span>Unban</span>';
					panel += '</button>';
				}
				panel += '</div>';
			}
        } else {
            var panel = '';
        }
		var name = data.username;
		var type = data.admin ? 'isAdmin' : (data.moder ? 'isModerator' : '');
        if(data.admin) name = '<span class="admin-badge isAdmin" data-toggle="tooltip" data-placement="top" title="Admin"><span class=""><svg class="icon icon-a"><use xlink:href="/img/symbols.svg#icon-a"></use></svg></span></span> Admin ';
        if(data.moder) name = '<span class="admin-badge isModerator" data-toggle="tooltip" data-placement="top" title="Moder"><span class=""><svg class="icon icon-m"><use xlink:href="/img/symbols.svg#icon-m"></use></svg></span></span> ' + data.username;
        if(data.youtuber) name = '<span class="admin-badge isYouTuber" data-toggle="tooltip" data-placement="top" title="YouTuber"><span class=""><svg class="icon icon-y"><use xlink:href="/img/symbols.svg#icon-y"></use></svg></span></span> ' + data.username;
		var messages = data.messages;
		$('.chat-conversation-inner').append(
			'<div class="message-block user_' + data.unique_id + '" id="chatm_' + data.time2 + '" >' +
			'<div class="message-avatar ' + type + '"><img src="' + data.avatar + '" alt=""></div>' +
			'<div class="message-content">' +
			'<div>' +
			'<button class="user-link" type="button" data-id="' + data.unique_id + '">' +
			'<span class="sanitize-name">' +
			'<span class="sanitize-text">' + name + '<span>&nbsp;</span></span>' +
			'</span>' +
			'</button>' +
			'<div class="message-text">' + messages + '</div>' +
			'</div>' +
			'</div>' +
			'' + panel + '' +
			'</div>'
		);
        if($('.chat-conversation-inner .message-block').length >= 20) $('.chat-conversation-inner .message-block:nth-child(1)').remove();
		container.scrollTop = container.scrollHeight - container.clientHeight;
		ps.update();
	})
	.on('chatdel', function (data) {
		$('#chatm_' + data.time2).remove();
		container.scrollTop = container.scrollHeight - container.clientHeight;
		ps.update();
	})
	.on('clear', function (data) {
		$('.chat-conversation-inner').html('');
	})
	.on('ban_message', function(data) {
		if(USER_ID == data.unique_id && data.ban) {
			$('.chat-message-input').remove();
			$('.chat').append('<div class="chat-ban-block"><div class="title">Chat blocked!</div><button type="button" class="btn btn-light unbanMe"><span>Unlock ( -0.00009000 <svg class="icon icon-coin balance"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg> )</span></button></div>');
		}
		if(USER_ID == data.unique_id && !data.ban) {
			$('.chat-ban-block').remove();
			$('.chat').append('<div class="chat-message-input"><div class="chat-textarea"><div class="chat-editable" contenteditable="true"></div></div><div class="chat-controls"><button class="item" id="smilesBlock" data-toggle="popover" data-placement="top" data-html="true"><svg class="icon icon-smile"><use xlink:href="/img/symbols.svg#icon-smile"></use></svg></button><button type="submit" class="item sendMessage"><svg class="icon icon-send"><use xlink:href="/img/symbols.svg#icon-send"></use></svg></button></div></div>');
		}
	});
	
	if(USER_ID == null) $('#confirmAgeModal').modal('show');
});

function initBalance() {
	if(!localStorage.getItem('balance')) localStorage.setItem('balance', 'balance');
	if(localStorage.getItem('balance') == 'balance') {
		$('.deposit-wrap .dropdown-toggle .selected').removeClass('bonus');
		$('.deposit-wrap .dropdown-toggle .selected').addClass('balance');
		$('#exchange').remove();
		$('#wallet').remove();
		$('.deposit-block').append('<button type="button" class="btn" id="wallet">Wallet</button>');
		type = 'balance';
	} else {
		$('.deposit-wrap .dropdown-toggle .selected').addClass('bonus');
		$('.deposit-wrap .dropdown-toggle .selected').removeClass('balance');
		$('#exchange').remove();
		$('#wallet').remove();
		$('.deposit-block').append('<button type="button" class="btn btn-light-gray" id="exchange">Exchange</button>');
		type = 'bonus';
	}
	var value = $('.balance-item.' + type + ' #'+ type +'_bal').text();
	$('.deposit-block #balance').text(value);
}


function copyToClipboard(element) {
    var $temp = $('<input>'),
		clear;
	$('.copy-tooltip').removeClass('visible');
	clearInterval(clear);
    $('body').append($temp);
    $temp.val($(element).val()).select();
    document.execCommand('copy');
    $temp.remove();
    $('.copy-tooltip').addClass('visible');
	clear = setInterval(function() {
		clearInterval(clear);
		$('.copy-tooltip').removeClass('visible');
	}, 1000)
}