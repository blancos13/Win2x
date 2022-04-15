@extends('admin')

@section('content')
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Edit user</h3>
	</div>
</div>

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="row">
		<div class="col-xl-4">
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label" style="width: 100%;">
						<h3 class="kt-portlet__head-title text-center" style="width: 100%;">
							{{$user->username}}
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget28">
						<div class="kt-widget28__visual" style="background: url({{$user->avatar}}) bottom center no-repeat"></div>
						<div class="kt-widget28__wrapper kt-portlet__space-x">
							<div class="tab-content">
								<div id="menu11" class="tab-pane active">
									<div class="kt-widget28__tab-items">
										<div class="kt-widget12">
											@if(!$user->fake)
											<div class="kt-widget12__content">
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Deposit amount</span> 
														<span class="kt-widget12__value">{{$pay}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Withdraw amount</span> 
														<span class="kt-widget12__value">{{$withdraw}}$</span>	
													</div>		 	 
												</div>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Amount of exchanges</span> 
														<span class="kt-widget12__value">{{$exchanges}}$</span>
													</div>
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet Jackpot
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$jackpotWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$jackpotLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet Wheel
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$wheelWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$wheelLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet Crash
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$crashWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$crashLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet PvP
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$coinWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$coinLose}}$</span>	
													</div>		 	 
												</div>
											</div>
	
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet Battle
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$battleWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$battleLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Bet Dice
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$diceWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$diceLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Total
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Win</span> 
														<span class="kt-widget12__value">{{$betWin}}$</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Lose</span> 
														<span class="kt-widget12__value">{{$betLose}}$</span>	
													</div>		 	 
												</div>
											</div>
											@endif
										</div>
									</div>					      	 		      	
								</div>					     
							</div>
						</div>			 	 
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-8">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							User information
						</h3>
					</div>
				</div>
				<!--begin::Form-->
				<form class="kt-form" method="post" action="/admin/user/save">
					<div class="kt-portlet__body">
						<input name="id" value="{{$user->id}}" type="hidden">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Last & First Name:</label>
								<input type="text" class="form-control" value="{{$user->username}}" disabled>
							</div>
							<div class="col-lg-6">
								<label class="">IP:</label>
								<input type="text" class="form-control" value="{{$user->ip}}" disabled>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Balance:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="balance" value="{{$user->balance}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-dollar"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>Bonuses:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="bonus" value="{{$user->bonus}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-dollar"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-12">
								<label>Role:</label>
								<select class="form-control" name="priv">
									<option value="admin" @if($user->is_admin) selected @endif>Admin</option>
									<option value="moder" @if($user->is_moder) selected @endif>Moder</option>
									<option value="youtuber" @if($user->is_youtuber) selected @endif>YouTube`r</option>
									<option value="user" @if(!$user->is_admin && !$user->is_moder && !$user->is_youtuber) selected @endif>User</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Ban on site:</label>
								<select class="form-control" name="ban">
									<option value="0" @if($user->ban == 0) selected @endif>No</option>
									<option value="1" @if($user->ban == 1) selected @endif>Yes</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>The reason for the ban on the website:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="ban_reason" value="{{$user->ban_reason}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Ban in chat to:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat" value="{{ !is_null($user->banchat) ? \Carbon\Carbon::parse($user->banchat)->format('d.m.Y H:i:s') : '' }}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-calendar-o"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>The reason for the ban in the chat:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat_reason" value="{{$user->banchat_reason}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="kt-portlet__foot kt-portlet__foot--solid">
						<div class="kt-form__actions">
							<div class="row">
								<div class="col-12">
									<button type="submit" class="btn btn-brand">Save</button>
								</div>
							</div>
						</div>
					</div>
				</form>
				<!--end::Form-->
			</div>
		</div>
	</div>
</div>
@endsection