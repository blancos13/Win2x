var url;
$(document).ready(function() {
	initAdmin();
	if(admin == 1) url = 'admin';
	if(moder == 1) url = 'moder';
	$(document).on('click', '.btnBan', function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
    	$('#banModal').find('#banName').text(name);
    	$('#banModal').find('input[name="user_ban_id"]').val(id);
		$('#banModal').modal('show');
	});
	
	$('#banModal').on('hidden.bs.modal', function (e) {
		$('#banModal').find('#banName').text('');
    	$('#banModal').find('input').val('');
	});
	
	$(document).on('click', '.banThis', function() {
		var id = $('#banModal').find('input[name="user_ban_id"]').val();
		var time = $('#banTime').val();
		var reason = $('#banReason').val();
		$.ajax({
            url : '/'+ url +'/ban',
            type : 'post',
            data : {
                id : id,
				time : time,
				reason : reason
            },
            success : function(data) {
				if(data.success) {
					$('#banModal').modal('hide');
					$('#banModal').find('input').val('');
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
	
	$(document).on('click', '.btnUnBan', function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
    	$('#unbanModal').find('#unbanName').text(name);
    	$('#unbanModal').find('input[name="user_unban_id"]').val(id);
		$('#unbanModal').modal('show');
	});
	
	$('#unbanModal').on('hidden.bs.modal', function (e) {
		$('#unbanModal').find('#unbanName').text('');
    	$('#unbanModal').find('input[name="user_unban_id"]').val('');
	});
	
	$(document).on('click', '.unbanThis', function() {
		var id = $('#unbanModal').find('input[name="user_unban_id"]').val();
		$.ajax({
            url : '/'+ url +'/unban',
            type : 'post',
            data : {
                id : id
            },
            success : function(data) {
				if(data.success) {
					$('#unbanModal').modal('hide');
					if($('#bannedModal').hasClass('show')) {
						$('#bannedModal .userban_' + id).remove();
					}
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
	
	$(document).on('click', '.clearChat', function() {
		$.ajax({
            url : '/'+ url +'/clear',
            type : 'post',
            success : function(data) {
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
	
	$(document).on('click', '.banned-btn', function() {
		$.ajax({
            url : '/'+ url +'/getBanned',
            type : 'post',
            success : function(data) {
				if(data.success) {
					var html = '';
					for(var i = 0; i < data.users.length; i++) {
						html += '<tr class="userban_' + data.users[i].unique_id + '">';
						html += '<td class="username">';
						html += '<button type="button" class="btn btn-link" data-id="' + data.users[i].unique_id + '">';
						html += '<span class="sanitize-user">';
						html += '<div class="sanitize-avatar"><img src="' + data.users[i].avatar + '" alt=""></div>';
						html += '<span class="sanitize-name">' + data.users[i].username + '</span>';
						html += '</span>';
						html += '</button>';
						html += '</td>';
						html += '<td>'+ timeConverter(data.users[i].banchat) +'</td>';
						html += '<td>'+ (data.users[i].banchat_reason ? data.users[i].banchat_reason : 'Unspecified') +'</td>';
						html += '<td>';
						html += '<button type="button" class="btn btn-light btnUnBan" data-name="' + data.users[i].username + '" data-id="' + data.users[i].unique_id + '"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg><span>Unban</span></button>';
						html += '</td>';
						html += '</tr>';
					}
					$('#bannedList').html(html);
					$('#bannedModal').modal('show');
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
	
	$(document).on('click', '.toggle-btn', function() {
		if(!$(this).is('.active')) {
			$(this).addClass('active');
			localStorage.setItem('admin', 1);
			$('#optional').val(1);
		} else {
			$(this).removeClass('active');
			localStorage.setItem('admin', 0);
			$('#optional').val(0);
		}
	});
	
	var socket = io.connect(':7777');
	socket
	.on('ban_message', function(data) {
		if(data.ban) {
			$('.message-block.user_' + data.unique_id + ' .btnBan').remove();
			$('.message-block.user_' + data.unique_id + ' .delete').append('<button type="button" class="btn btn-light btnUnBan" data-name="' + data.username + '" data-id="' + data.unique_id + '"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg><span>Unban</span></button>');
		} else {
			$('.message-block.user_' + data.unique_id + ' .btnUnBan').remove();
			$('.message-block.user_' + data.unique_id + ' .delete').append('<button type="button" class="btn btn-light btnBan" data-name="' + data.username + '" data-id="' + data.unique_id + '"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-ban"></use></svg><span>Ban</span></button>');
		}
	})
});

function chatdelet(id) {
	$.post('/'+ url +'/chatdel', {messages: id}, function (data) {
		if(data) {
			$.notify({
				type: data.status,
				message: data.message
			});
		}
	});
}

function timeConverter(UNIX_timestamp) {
	var a = new Date(UNIX_timestamp * 1000);
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var hour = a.getHours();
	var min = a.getMinutes();
	var sec = a.getSeconds();
	var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
	return time;
}

function initAdmin() {
	if(!localStorage.getItem('admin')) localStorage.setItem('admin', 0);
	if(localStorage.getItem('admin') == 1) {
		$('.toggle-btn').addClass('active');
		$('#optional').val(1);
	} else {
		$('.toggle-btn').removeClass('active');
		$('#optional').val(0);
	}
}