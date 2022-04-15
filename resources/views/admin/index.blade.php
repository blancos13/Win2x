@extends('admin')

@section('content')
<script type="text/javascript" src="/js/chart.min.js"></script>
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Statistics</h3>
	</div>
</div>

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="kt-portlet">
		<div class="kt-portlet__body  kt-portlet__body--fit">
			<div class="row row-no-padding row-col-separator-xl">

				<div class="col-md-12 col-lg-6 col-xl-3">
					<!--begin::Total Profit-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									Deposits on
								</h4>
								<span class="kt-widget24__desc">
									for today
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-success">
								{{$pay_today}}<i class="la la-dollar"></i>
							</span>	 
						</div>				   			      
					</div>
					<!--end::Total Profit-->
				</div>

				<div class="col-md-12 col-lg-6 col-xl-3">
					<!--begin::New Feedbacks-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									Deposits on
								</h4>
								<span class="kt-widget24__desc">
									for 7 days
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-success">
							   {{$pay_week}}<i class="la la-dollar"></i>
							</span>	 
						</div>			   			      
					</div>				
					<!--end::New Feedbacks--> 
				</div>

				<div class="col-md-12 col-lg-6 col-xl-3">
					<!--begin::New Orders-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									Deposits on
								</h4>
								<span class="kt-widget24__desc">
									per month
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-success">
								{{$pay_month}}<i class="la la-dollar"></i>
							</span>	 
						</div>			   			      
					</div>				
					<!--end::New Orders--> 
				</div>

				<div class="col-md-12 col-lg-6 col-xl-3">
					<!--begin::New Users-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									Deposits on
								</h4>
								<span class="kt-widget24__desc">
									for all time
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-success">
								{{$pay_all}}<i class="la la-dollar"></i>
							</span>	 
						</div>		   			      
					</div>				
					<!--end::New Users--> 
				</div>

			</div>
		</div>
	</div>
	<div class="kt-portlet">
		<div class="kt-portlet__body  kt-portlet__body--fit">
			<div class="row row-no-padding row-col-separator-xl">

				<div class="col-md-12 col-lg-6 col-xl-6">
					<!--begin::Total Profit-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									Users
								</h4>
								<span class="kt-widget24__desc">
									total
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-brand">
								{{$usersCount}}<i class="la la-user"></i>
							</span>	 
						</div>				   			      
					</div>
					<!--end::Total Profit-->
				</div>

				<div class="col-md-12 col-lg-6 col-xl-6">
					<!--begin::New Orders-->
					<div class="kt-widget24">
						<div class="kt-widget24__details">
							<div class="kt-widget24__info">
								<h4 class="kt-widget24__title">
									For withdraw
								</h4>
								<span class="kt-widget24__desc">
									total amount
								</span>
							</div>

							<span class="kt-widget24__stats kt-font-danger">
								{{$with_req}}<i class="la la-dollar"></i>
							</span>	 
						</div>			   			      
					</div>				
					<!--end::New Orders--> 
				</div>

			</div>
		</div>
	</div>
	<div class="kt-portlet">
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="row row-no-padding row-col-separator-xl">
				<div class="col-md-12 col-lg-12 col-xl-4">
					<!--begin:: Widgets/Stats2-1 -->
					<div class="kt-widget1">
						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Commission Jackpot</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_jackpot}}<i class="la la-dollar"></i></span>
						</div>

						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Commission PvP</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_pvp}}<i class="la la-dollar"></i></span>
						</div>

						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Commission Battle</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_battle}}<i class="la la-dollar"></i></span>
						</div>

					</div>
					<!--end:: Widgets/Stats2-1 -->
				</div>
				<div class="col-md-12 col-lg-12 col-xl-4">
					<!--begin:: Widgets/Stats2-2 -->
					<div class="kt-widget1">
						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Profit Wheel</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_wheel}}<i class="la la-dollar"></i></span>
						</div>

						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Profit Dice</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_dice}}<i class="la la-dollar"></i></span>
						</div>

						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Profit Crash</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_crash}}<i class="la la-dollar"></i></span>
						</div>

					</div>
					<!--end:: Widgets/Stats2-2 -->
				</div>
				<div class="col-md-12 col-lg-12 col-xl-4">
					<!--begin:: Widgets/Stats2-3 -->
					<div class="kt-widget1">
						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Profit exchanges</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit_exchange}}<i class="la la-dollar"></i></span>
						</div>
						
						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Total Profit</h3>
							</div>
							<span class="kt-widget1__number kt-font-success">{{$profit}}<i class="la la-dollar"></i></span>
						</div>

						<div class="kt-widget1__item">
							<div class="kt-widget1__info">
								<h3 class="kt-widget1__title">Ref. system\Promo (expense)</h3>
							</div>
							<span class="kt-widget1__number kt-font-danger">{{$profit_ref}}<i class="la la-dollar"></i></span>
						</div>
					</div>
					<!--end:: Widgets/Stats2-3 -->
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xl-6">
			<div class="kt-portlet kt-portlet--height-fluid">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Registration for current month
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body kt-portlet__body--fluid">
					<div class="kt-widget12">
						<div class="kt-widget12__chart" style="height:250px;">
							<canvas id="authChart"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-6">
			<div class="kt-portlet kt-portlet--height-fluid">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Deposite for current month
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body kt-portlet__body--fluid">
					<div class="kt-widget12">
						<div class="kt-widget12__chart" style="height:250px;">
							<canvas id="depsChart"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xl-4">

			<!--begin:: Widgets/Support Tickets -->
			<div class="kt-portlet kt-portlet--height-fluid">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Chat
						</h3>
					</div>
					<div class="kt-portlet__head-toolbar">
						<div class="dropdown dropdown-inline">
							<button type="button" class="btn btn-clean btn-sm btn-icon-md btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="flaticon-more-1"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right">
								<ul class="kt-nav">
									<li class="kt-nav__item">
										<a class="kt-nav__link clearChat">
											<i class="kt-nav__link-icon flaticon-delete"></i>
											<span class="kt-nav__link-text">Clear</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="kt-portlet__body" id="chat_app">
					<div class="kt-widget3 chat-scroll kt-scroll" data-scroll="true" data-height="345">
						@if($messages != 0) @foreach($messages as $sms)
						<div class="kt-widget3__item chat_mes" id="chatm_{{$sms['time2']}}">
							<div class="kt-widget3__header">
								<div class="kt-widget3__user-img">
									<img class="kt-widget3__img" src="{{$sms['avatar']}}" alt="">
								</div>
								<div class="kt-widget3__info">
									<span class="kt-widget3__username">
										{{$sms['username']}}
									</span><br>
									<span class="kt-widget3__time">
										{{$sms['time']}}
									</span>
								</div>
								<span class="kt-widget3__status">
									<button class="btn btn-sm btn-label-brand btn-bold" onclick="chatdelet({{$sms['time2']}})">Delete</button>
								</span>
							</div>
							<div class="kt-widget3__body">
								<p class="kt-widget3__text">
									{!!$sms['messages']!!}
								</p>
							</div>
						</div>
						@endforeach @endif
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-4">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Choice of winners Jackpot
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<ul class="nav nav-tabs nav-fill" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#room_easy">EASY</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#room_medium">MEDIUM</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#room_hard">HARD</a>
						</li>
					</ul>
					<div class="tab-content kt-scroll" data-scroll="true" data-height="507">
						<div class="tab-pane active" id="room_easy" role="tabpanel">
							<div class="kt-timeline-v1__item-body">
								<div class="kt-widget4" id="chance_easy">
									@foreach($chances_easy as $user)
									<div class="kt-widget4__item">
										<div class="kt-widget4__pic">
											<img src="{{ $user['avatar'] }}" alt="">
										</div>
										<div class="kt-widget4__info">
											<span class="kt-widget4__username">
												{{ $user['username'] }}
											</span>
											<p class="kt-widget4__text">
												{{ $user['sum'] }}$/{{ $user['chance'] }}%
											</p>
										</div>
										<button class="btn btn-sm btn-outline-success btn-bold" onclick="gotRoulette('{{ $user['game_id'] }}', {{ $user['id'] }})">Choice</button>
									</div>
									@endforeach
								</div>
							</div>
						</div>
						<div class="tab-pane" id="room_medium" role="tabpanel">
							<div class="kt-timeline-v1__item-body">
								<div class="kt-widget4" id="chance_medium">
									@foreach($chances_medium as $user)
									<div class="kt-widget4__item">
										<div class="kt-widget4__pic">
											<img src="{{ $user['avatar'] }}" alt="">
										</div>
										<div class="kt-widget4__info">
											<span class="kt-widget4__username">
												{{ $user['username'] }}
											</span>
											<p class="kt-widget4__text">
												{{ $user['sum'] }}$/{{ $user['chance'] }}%
											</p>
										</div>
										<button class="btn btn-sm btn-outline-success btn-bold" onclick="gotRoulette('{{ $user['game_id'] }}', {{ $user['id'] }})">Choice</button>
									</div>
									@endforeach
								</div>
							</div>
						</div>
						<div class="tab-pane" id="room_hard" role="tabpanel">
							<div class="kt-timeline-v1__item-body">
								<div class="kt-widget4" id="chance_hard">
									@foreach($chances_hard as $user)
									<div class="kt-widget4__item">
										<div class="kt-widget4__pic">
											<img src="{{ $user['avatar'] }}" alt="">
										</div>
										<div class="kt-widget4__info">
											<span class="kt-widget4__username">
												{{ $user['username'] }}
											</span>
											<p class="kt-widget4__text">
												{{ $user['sum'] }}$/{{ $user['chance'] }}%
											</p>
										</div>
										<button class="btn btn-sm btn-outline-success btn-bold" onclick="gotRoulette('{{ $user['game_id'] }}', {{ $user['id'] }})">Choice</button>
									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-xl-4">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Choice color Wheel
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="text-center">
						<button type="button" class="btn btn-primary gotWheel" data-color="black">x2</button>
						<button type="button" class="btn btn-danger gotWheel" data-color="red">x3</button>
						<button type="button" class="btn btn-success gotWheel" data-color="green">x5</button>
						<button type="button" class="btn btn-warning gotWheel" data-color="yellow">x50</button>
					</div>
				</div>
			</div>
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Set num Crash
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Enter number" id="multiplier_crash">
						<div class="input-group-append">
							<button class="btn btn-primary gotCrash" type="button">set</button>
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Choise team Battle
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="text-center">
						<button type="button" class="btn btn-danger gotBattle" data-color="red">Red</button>
						<button type="button" class="btn btn-info gotBattle" data-color="blue">Blue</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xl-4">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Latest deposites
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget3 kt-scroll" data-scroll="true" data-height="616">
						@foreach($last_dep as $pay)
						<div class="kt-widget3__item">
							<div class="kt-widget3__header">
								<div class="kt-widget3__user-img">							 
									<img class="kt-widget3__img" src="{{$pay['avatar']}}" alt="">    
								</div>
								<div class="kt-widget3__info">
									<a href="/admin/user/{{$pay['id']}}" class="kt-widget3__username">
									{{$pay['username']}}
									</a><br> 
									<span class="kt-widget3__time">
									{{ Carbon\Carbon::parse($pay['date'])->diffForHumans() }}
									</span>		 
								</div>
								<span class="kt-widget3__status kt-font-success">
									{{$pay['sum']}} <i class="la la-dollar"></i>
								</span>	
							</div>
						</div>
						@endforeach
					</div>
				</div>		
			</div>
		</div>
		<div class="col-xl-4">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Latest users
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget3 kt-scroll" data-scroll="true" data-height="616">
						@foreach($users as $user)
						<div class="kt-widget3__item">
							<div class="kt-widget3__header">
								<div class="kt-widget3__user-img">							 
									<img class="kt-widget3__img" src="{{$user->avatar}}" alt="">    
								</div>
								<div class="kt-widget3__info">
									<a href="/admin/user/{{$user->id}}" class="kt-widget3__username">
									{{$user->username}}
									</a><br> 
									<span class="kt-widget3__time">
									Referral: @if($user->ref_id)<a href="/admin/user/{{ App\User::findRef($user->ref_id)->id }}">{{ App\User::findRef($user->ref_id)->username }}</a>@else No @endif
									</span>		 
								</div>
								<span class="kt-widget3__status kt-font-success">
									{{ Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
								</span>	
							</div>
						</div>
						@endforeach
					</div>
				</div>		
			</div>
		</div>
		<div class="col-xl-4">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							The richest
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget3 kt-scroll" data-scroll="true" data-height="616">
						@foreach($userTop as $top)
						<div class="kt-widget3__item">
							<div class="kt-widget3__header">
								<div class="kt-widget3__user-img">							 
									<img class="kt-widget3__img" src="{{$top->avatar}}" alt="">    
								</div>
								<div class="kt-widget3__info">
									<a href="/admin/user/{{$top->id}}" class="kt-widget3__username">
									{{$top->username}}
									</a>	 
								</div>
								<span class="kt-widget3__status kt-font-success">
									{{ $top->balance }} <i class="la la-dollar"></i>
								</span>	
							</div>
						</div>
						@endforeach
					</div>
				</div>		
			</div>
		</div>
	</div>
	
</div>

<script>
$(document).ready(function() {
	$.ajax({
		method: 'POST',
		url: '/admin/getUserByMonth',
		success: function (res) {
			var authChart = 'authChart';
			if ($('#'+authChart).length > 0) {
				var months = [];
				var users = [];

				$.each(res, function(index, data) {
					months.push(data.date);
					users.push(data.count);
				});

				var lineCh = document.getElementById(authChart).getContext("2d");

				var chart = new Chart(lineCh, {
					type: 'line',
					data: {
						labels: months,
						datasets: [{
							label: "",
							tension:0.4,
							backgroundColor: 'transparent',
							borderColor: '#2c80ff',
							pointBorderColor: "#2c80ff",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 2,
							pointHoverRadius: 6,
							pointHoverBackgroundColor: "#fff",
							pointHoverBorderColor: "#2c80ff",
							pointHoverBorderWidth: 2,
							pointRadius: 6,
							pointHitRadius: 6,
							data: users,
						}]
					},
					options: {
						legend: {
							display: false
						},
						maintainAspectRatio: false,
						tooltips: {
							callbacks: {
								title: function(tooltipItem, data) {
									return 'Date : ' + data['labels'][tooltipItem[0]['index']];
								},
								label: function(tooltipItem, data) {
									return data['datasets'][0]['data'][tooltipItem['index']] + ' user(s)' ;
								}
							},
							backgroundColor: '#eff6ff',
							titleFontSize: 13,
							titleFontColor: '#6783b8',
							titleMarginBottom:10,
							bodyFontColor: '#9eaecf',
							bodyFontSize: 14,
							bodySpacing:4,
							yPadding: 15,
							xPadding: 15,
							footerMarginTop: 5,
							displayColors: false
						},
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero: true,
									fontSize:12,
									fontColor:'#9eaecf',
									stepSize: Math.ceil(users/5)
								},
								gridLines: { 
									color: "#e5ecf8",
									tickMarkLength:0,
									zeroLineColor: '#e5ecf8'
								},

							}],
							xAxes: [{
								ticks: {
									beginAtZero: true,
									fontSize:12,
									fontColor:'#9eaecf',
									source: 'auto',
								},
								gridLines: {
									color: "transparent",
									tickMarkLength:20,
									zeroLineColor: '#e5ecf8',
								},
							}]
						}
					}
				});
			}
		}
	});
	$.ajax({
		method: 'POST',
		url: '/admin/getDepsByMonth',
		success: function (res) {
			var depsChart = 'depsChart';
			if ($('#'+depsChart).length > 0) {
				var months = [];
				var deps = [];

				$.each(res, function(index, data) {
					months.push(data.date);
					deps.push(data.sum);
				});

				var lineCh = document.getElementById(depsChart).getContext("2d");

				var chart = new Chart(lineCh, {
					type: 'line',
					data: {
						labels: months,
						datasets: [{
							label: "",
							tension:0.4,
							backgroundColor: 'transparent',
							borderColor: '#2c80ff',
							pointBorderColor: "#2c80ff",
							pointBackgroundColor: "#fff",
							pointBorderWidth: 2,
							pointHoverRadius: 6,
							pointHoverBackgroundColor: "#fff",
							pointHoverBorderColor: "#2c80ff",
							pointHoverBorderWidth: 2,
							pointRadius: 6,
							pointHitRadius: 6,
							data: deps,
						}]
					},
					options: {
						legend: {
							display: false
						},
						maintainAspectRatio: false,
						tooltips: {
							callbacks: {
								title: function(tooltipItem, data) {
									return 'Date : ' + data['labels'][tooltipItem[0]['index']];
								},
								label: function(tooltipItem, data) {
									return data['datasets'][0]['data'][tooltipItem['index']] + '$' ;
								}
							},
							backgroundColor: '#eff6ff',
							titleFontSize: 13,
							titleFontColor: '#6783b8',
							titleMarginBottom:10,
							bodyFontColor: '#9eaecf',
							bodyFontSize: 14,
							bodySpacing:4,
							yPadding: 15,
							xPadding: 15,
							footerMarginTop: 5,
							displayColors: false
						},
						scales: {
							yAxes: [{
								ticks: {
									beginAtZero:true,
									fontSize:12,
									fontColor:'#9eaecf',
									stepSize: Math.ceil(deps/5)
								},
								gridLines: { 
									color: "#e5ecf8",
									tickMarkLength:0,
									zeroLineColor: '#e5ecf8'
								},

							}],
							xAxes: [{
								ticks: {
									fontSize:12,
									fontColor:'#9eaecf',
									source: 'auto',
								},
								gridLines: {
									color: "transparent",
									tickMarkLength:20,
									zeroLineColor: '#e5ecf8',
								},
							}]
						}
					}
				});
			}
		}
	});
});
</script>
@endsection