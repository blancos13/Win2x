@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/jackpotHistory.css">
<script type="text/javascript" src="/js/jackpotHistory.js"></script>
<div class="section">
    <div class="history-component">
        <div class="history-head">
            <h1 class="history-caption">History</h1>
            <div class="history-link"><a class="btn btn-light" href="/">Back</a></div>
        </div>
		<div class="button-group__wrap">
			<div class="button-group__content rooms">
				@foreach($rooms->sortBy('id') as $r)
				<a class="btn {{$r->name}}" data-room="{{$r->name}}"><span>{{$r->title}}</span></a>
				@endforeach
			</div>
		</div>
        <div class="game-stats">
			<div class="table-heading">
				<div class="thead">
					<div class="tr">
						<div class="th">#</div>
						<div class="th">Winner</div>
						<div class="th">Win</div>
						<div class="th">Chance</div>
						<div class="th">Ticket</div>
						<div class="th"></div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap" style="transform: translateY(0px);">
					<table class="table">
						<tbody id="history"></tbody>
					</table>
				</div>
			</div>
		</div>
    </div>
</div>
@endsection