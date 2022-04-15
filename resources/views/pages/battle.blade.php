@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/battle.css">
<script src="https://d3js.org/d3-path.v1.min.js"></script>
<script src="https://d3js.org/d3-shape.v1.min.js"></script>
<script type="text/javascript" src="/js/battle.js"></script>
<div class="section game-section">
    <div class="container">
        <div class="game">
            <div class="game-sidebar">
                <div class="sidebar-block">
                    <div class="bet-component">
                        <div class="bet-form">
                            <div class="form-row">
                                <label>
                                    <div class="form-label"><span>Bet Amount</span></div>
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
                                </label>
                            </div>
                            <div class="command-type btnToggle">
								<button class="btn-bet bet-red" data-team="red">
									<div class="bet-chance">
										<div class="chance-text">
											<span id="red_persent">{{$chances[0]}}%</span><br>Red
											<p id="red_tickets">1 - {{$tickets[0]}}</p>
										</div>
									</div>
									<div class="bet-text" id="red_factor">{{ $factor[0] ? $factor[0].'x' : '2x' }}</div>
								</button>
								<button class="btn-bet bet-blue" data-team="blue">
									<div class="bet-chance">
										<div class="chance-text">
											<span id="blue_persent">{{$chances[1]}}%</span><br>Blue
											<p id="blue_tickets">{{$tickets[1]}} - 1000</p>
										</div>
									</div>
									<div class="bet-text" id="blue_factor">{{ $factor[1] ? $factor[1].'x' : '2x' }}</div>
								</button>
							</div>
                            <button type="button" class="btn btn-green btn-play"><span>Make bet</span></button>
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
				<div class="game_Wheel">
					<div class="progress-wrap">
						<div class="progress-item left">
							<div class="title">Min Bet: <span id="minBet">{{$settings->battle_min_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
							<div class="title">Max Bet: <span id="maxBet">{{$settings->battle_max_bet}}</span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
						</div>
						<div class="progress-item right">
							<div class="title">Game #<span id="gameId">{{$game->id}}</span></div>
						</div>
					</div>
					<div class="wheel-game">
						<div class="wheel-content">
							<svg class="UsersInterestChart" width="400" height="400">
								<g class="chart" transform="translate(200, 200)">
									<g class="timer" transform="translate(0,0)">
										<g class="bets" id="circle" style="transform: rotate(0deg);">
											<path id="blue" fill="#4986f5" stroke-width="5px" d="M1.1021821192326179e-14,-200A200,200,0,1,1,1.1021821192326179e-14,200L9.491012693391987e-15,180A180,180,0,1,0,9.491012693391987e-15,-180Z" transform="rotate(0)" style="opacity: 1;"></path>
											<path id="red" fill="#e86376" stroke-width="5px" d="M1.1021821192326179e-14,200A200,200,0,1,1,-3.3065463576978534e-14,-200L-2.847303808017596e-14,-180A180,180,0,1,0,9.491012693391987e-15,180Z" transform="rotate(0)" style="opacity: 1;"></path>
										</g>
									</g>
								</g>
								<polygon points="200,10 220,40 180,40" style="fill: #ffffff;stroke: rgba(255, 255, 255, 0.05);stroke-width: 5px;"></polygon>
							</svg>
							<div class="time">
								<div class="block">
									<div class="title">Bank</div>
									<div class="value bank" id="value" style="color: #7b8893"><span id="red_sum" style="color: #e86376;">{{$bank[0]}}</span>/<span id="blue_sum" style="color: #4986f5;">{{$bank[1]}}</span></div>
									<div class="line"></div>
									<div class="title">To start</div>
									<div class="value" id="timer">{{$time[0]}}:{{$time[1]}}</div>
								</div>
							</div>
						</div>
					</div>
					<div class="history_wrapper">
						<div class="history_history">
							@foreach($lastwins as $g)
							<div class="item history_item history_{{ $g->winner_team }} checkGame" data-hash="{{$g->hash}}"></div>
							@endforeach
						</div>
					</div>
					<div class="hash">
						<span class="title">HASH:</span> <span class="text">{{ $game->hash }}</span>
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
						<div class="th">Team</div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody>
							@foreach($bets as $b)
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="{{$b->user->unique_id}}">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="{{$b->user->avatar}}" alt=""></div>
											<span class="sanitize-name">{{$b->user->username}}</span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span>{{$b->price}}</span>
											<svg class="icon icon-coin {{$b->balType}}">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td><span class="bet-type bet_{{$b->color}}">{{$b->color == 'red' ? 'Red' : 'Blue'}}</span></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		build({{ $chances[1] / 100 }});
	});
</script>
@endsection