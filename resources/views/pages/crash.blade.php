@extends('layout')

@section('content')
@if($bet)
<script>
	window.bet = parseInt('{{ $bet->price }}');
	window.isCashout = false;
	window.withdraw = parseFloat('{{ $bet->withdraw }}');
</script>
@endif
<link rel="stylesheet" href="{{ asset('/css/crash.css') }}">
<script src="{{ asset('/js/chart.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/crash.js') }}"></script>
<div class="section game-section">
    <div class="container">
        <div class="game crash-prefix">
            <div class="game-sidebar">
                <div class="sidebar-block">
                    <div class="bet-component">
                        <div class="bet-form">
                            <div class="form-row">
                                <label>
                                    <div class="form-label"><span>Bet amount</span></div>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <input type="text" name="sum" class="input-field no-bottom-radius" value="0.00" id="sum">
                                            <button type="button" class="btn btn-bet-clear" data-action="clear">
												<svg class="icon icon-close">
													<use xlink:href="/img/symbols.svg#icon-close"></use>
												</svg>
                                            </button>
                                            <div class="buttons-group no-top-radius">
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.01">+0.01</button>
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.10">+0.10</button>
                                                <button type="button" class="btn btn-action" data-action="plus" data-value="0.50">+0.50</button>
                                                <button type="button" class="btn btn-action" data-action="multiply" data-value="2">2X</button>
                                                <button type="button" class="btn btn-action" data-action="divide" data-value="2">1/2</button>
                                                <button type="button" class="btn btn-action" data-action="all">MAX</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
										<label>
											<div class="form-label"><span>Auto stop</span></div>
											<div class="form-field">
												<div class="input-valid">
													<input class="input-field" value="2.00" id="betout"><div class="input-suffix"><span>2.00</span>&nbsp;x</div>
												</div>
											</div>
										</label>
                                    </div>
                                </label>
                            </div>
                            <button type="button" class="btn btn-green btn-play" style="@if(!is_null($bet)) display : none; @endif"><span>Make bet</span></button>
                            <button type="button" class="btn btn-green btn-withdraw" style="@if(is_null($bet)) display : none; @endif"><span>Withdraw</span></button>
                        </div>
                        <div class="bet-footer">
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#fairModal">
                                <svg class="icon icon-fairness">
                                    <use xlink:href="/img/symbols.svg#icon-fairness"></use>
                                </svg><span>Fair game</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
			<div class="game-component">
				<div class="game-block">
					<div class="progress-wrap">
						<div class="progress-item left">
							<div class="title">Min sum: <span id="minBet">{{$settings->crash_min_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max sum: <span id="maxBet">{{$settings->crash_max_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
						<div class="progress-item right">
							<div class="title">Game #<span id="gameId">{{$game['id']}}</span></div>
						</div>
					</div>
					<div class="game-area__wrap">
						<div class="game-area">
							<div class="game-area-content">
								<div class="crash__connected">
									<canvas id="crashChart" height="642" width="800" style="width: 100%; height: auto;"></canvas>
									<h2><span id="chartInfo">Loading</span></h2>
								</div>
								<div class="hash">
									<span class="title">HASH:</span> <span class="text">{{ $game['hash'] }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="game-history__wrap">
					<div class="game-history">
						@foreach($history as $m)
						<div class="item checkGame" data-hash="{{$m->hash}}">
							<div class="item-bet" style="color: {{$m->color}};">x{{ number_format($m->multiplier, 2, '.', '') }}</div>
						</div>
						@endforeach
					</div>
				</div>
				@guest
				<div class="game-sign">
					<div class="game-sign-wrap">
						<div class="game-sign-block auth-buttons">
							To play, you must be authorized
							<button type="button" class="btn" data-toggle="modal" data-target="#signinModal">Log In</button>
						</div>
					</div>
				</div>
				@endguest
			</div>
        </div>
    </div>
</div>
<div class="section bets-section">
	<div class="container">
		<div class="game-stats">
			<div class="table-heading">
				<div class="thead">
					<div class="tr">
						<div class="th">User</div>
						<div class="th">Bet</div>
						<div class="th">Auto stop</div>
						<div class="th">Win</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody id="bets">
							@foreach($game['bets'] as $bet)
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{ $bet['user']['unique_id'] }}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{ $bet['user']['avatar'] }}" alt=""></div>
											<span class="sanitize-name">{{ $bet['user']['username'] }}</span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span>{{ $bet['price'] }}</span>
											<svg class="icon icon-coin {{ $bet['balType'] }}">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td>х{{ $bet['withdraw'] }}</td>
								<td>
									@if($bet['status'] == 1)
									<span class="bet-wrap win">
										<span>{{ $bet['won'] }}</span>
										<svg class="icon icon-coin">
											<use xlink:href="/img/symbols.svg#icon-coin"></use>
										</svg>
									</span>
									@else
									<span class="bet-wrap wait">
										<svg class="icon">
											<use xlink:href="/img/symbols.svg#icon-time"></use>
										</svg>
									</span>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection