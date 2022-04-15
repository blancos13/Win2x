

<?php $__env->startSection('content'); ?>
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Settings</h3>
	</div>
</div>

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="kt-portlet kt-portlet--tabs">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-toolbar">
				<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#site" role="tab" aria-selected="true">
							Site settings
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#jackpot" role="tab" aria-selected="false">
							Jackpot
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#wheel" role="tab" aria-selected="false">
							Wheel
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#crash" role="tab" aria-selected="false">
							Crash
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#pvp" role="tab" aria-selected="false">
							PvP
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#battle" role="tab" aria-selected="false">
							Battle
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#dice" role="tab" aria-selected="false">
							Dice
						</a>
					</li>
				</ul>
			</div>
		</div>
		<form class="kt-form" method="post" action="/admin/setting/save">
			<div class="kt-portlet__body">
				<div class="tab-content">
					<div class="tab-pane active" id="site" role="tabpanel">
						<div class="kt-section">
							<h3 class="kt-section__title">
								General setting:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Domain:</label>
									<input type="text" class="form-control" placeholder="domain.ru" value="<?php echo e($settings->domain); ?>" name="domain">
								</div>
								<div class="col-lg-4">
									<label>Sitename:</label>
									<input type="text" class="form-control" placeholder="sitename.ru" value="<?php echo e($settings->sitename); ?>" name="sitename">
								</div>
								<div class="col-lg-4">
									<label>Title:</label>
									<input type="text" class="form-control" placeholder="sitename.ru - short description" value="<?php echo e($settings->title); ?>" name="title">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Description for search engines:</label>
									<input type="text" class="form-control" placeholder="The description for the site..." value="<?php echo e($settings->description); ?>" name="description">
								</div>
								<div class="col-lg-4">
									<label>Keywords for search engines:</label>
									<input type="text" class="form-control" placeholder="website, name, domain, etc..." value="<?php echo e($settings->keywords); ?>" name="keywords">
								</div>
								<div class="col-lg-4">
									<label>Replace censored words in chat:</label>
									<input type="text" class="form-control" placeholder="i ❤ site" value="<?php echo e($settings->censore_replace); ?>" name="censore_replace">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Minimum amount for bonus exchange:</label>
									<input type="text" class="form-control" placeholder="1000" value="<?php echo e($settings->exchange_min); ?>" name="exchange_min">
								</div>
								<div class="col-lg-4">
									<label>Bonus exchange rate:</label>
									<input type="text" class="form-control" placeholder="2" value="<?php echo e($settings->exchange_curs); ?>" name="exchange_curs">
								</div>
								<div class="col-lg-4">
									<label>The amount of deposit to use the chat. 0 - Disabled</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->chat_dep); ?>" name="chat_dep">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Timed bonus interval (every N minutes)</label>
									<input type="text" class="form-control" placeholder="15" value="<?php echo e($settings->bonus_group_time); ?>" name="bonus_group_time">
								</div>
								<div class="col-lg-4">
									<label>Number of active referrals to receive the bonus:</label>
									<input type="text" class="form-control" placeholder="8" value="<?php echo e($settings->max_active_ref); ?>" name="max_active_ref">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Setup a referral system:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>What percentage of the winnings gets invited:</label>
									<input type="text" class="form-control" placeholder="Enter the percentage" value="<?php echo e($settings->ref_perc); ?>" name="ref_perc">
								</div>
								<div class="col-lg-4">
									<label>How much money is received by the invitee on a balance:</label>
									<input type="text" class="form-control" placeholder="Enter the amount" value="<?php echo e($settings->ref_sum); ?>" name="ref_sum">
								</div>
								<div class="col-lg-4">
									<label>The minimum amount for withdrawal from ref. accounts:</label>
									<input type="text" class="form-control" placeholder="Enter the amount" value="<?php echo e($settings->min_ref_withdraw); ?>" name="min_ref_withdraw">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Other settings:
							</h3>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Minimum deposit amount:</label>
									<input type="text" class="form-control" placeholder="Enter sum" value="<?php echo e($settings->min_dep); ?>" name="min_dep">
								</div>
								<div class="col-lg-3">
									<label>Maximum deposit amount:</label>
									<input type="text" class="form-control" placeholder="Enter sum" value="<?php echo e($settings->max_dep); ?>" name="max_dep">
								</div>
								<div class="col-lg-3">
									<label>The amount of deposits to make a withdraw:</label>
									<input type="text" class="form-control" placeholder="Enter sum" value="<?php echo e($settings->min_dep_withdraw); ?>" name="min_dep_withdraw">
								</div>
								<div class="col-lg-3">
									<label>Profit system ratio for antiminus (deposites * coef.):</label>
									<input type="text" class="form-control" placeholder="Enter coef" value="<?php echo e($settings->profit_koef); ?>" name="profit_koef">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Group settings FaceBook:
							</h3>
							<div class="form-group row">
								<div class="col-lg-6">
									<label>Group link FaceBook:</label>
									<input type="text" class="form-control" placeholder="https://facebook.com/..." value="<?php echo e($settings->vk_url); ?>" name="vk_url">
								</div>
								<div class="col-lg-6">
									<label>Link to support link:</label>
									<input type="text" class="form-control" placeholder="https://facebook.com/..." value="<?php echo e($settings->vk_support_link); ?>" name="vk_support_link">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								CoinPayments payment system settings:
							</h3>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Merchant ID:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->merchant_id); ?>" name="merchant_id">
								</div>
								<div class="col-lg-3">
									<label>IPN Secret:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->ipn_secret); ?>" name="ipn_secret">
								</div>
								<div class="col-lg-3">
									<label>Public key:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->public_key); ?>" name="public_key">
								</div>
								<div class="col-lg-3">
									<label>Private key:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->private_key); ?>" name="private_key">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Fee for transaction:</label>
									<input type="text" class="form-control" placeholder="Enter percentage" value="<?php echo e($settings->coinpayments_fee); ?>" name="coinpayments_fee">
								</div>
								<div class="col-lg-3">
									<label>Min. withdraw amount:</label>
									<input type="text" class="form-control" placeholder="Enter amount" value="<?php echo e($settings->coinpayments_min); ?>" name="coinpayments_min">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								PerfectMoney payment system settings:
							</h3>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Member ID:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->pm_uid); ?>" name="pm_uid">
								</div>
								<div class="col-lg-3">
									<label>Password:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->pm_pass); ?>" name="pm_pass">
								</div>
								<div class="col-lg-3">
									<label>USD wallet number:</label>
									<input type="text" class="form-control" placeholder="Uxxxxxxx" value="<?php echo e($settings->pm_usd_wallet); ?>" name="pm_usd_wallet">
								</div>
								<div class="col-lg-3">
									<label>Alternate Passphrase:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->pm_passphrase); ?>" name="pm_passphrase">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Fee for transaction:</label>
									<input type="text" class="form-control" placeholder="Enter percentage" value="<?php echo e($settings->pm_fee); ?>" name="pm_fee">
								</div>
								<div class="col-lg-3">
									<label>Min. withdraw amount:</label>
									<input type="text" class="form-control" placeholder="Enter amount" value="<?php echo e($settings->pm_min); ?>" name="pm_min">
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="jackpot" role="tabpanel">
						<div class="form-group">
							<label>Game fee in %:</label>
							<input type="text" class="form-control" placeholder="Enter the percentage" value="<?php echo e($settings->jackpot_commission); ?>" name="jackpot_commission">
						</div>
						<?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Room "<?php echo e($r->title); ?>":
							</h3>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Timer:</label>
									<input type="text" class="form-control" name="time_<?php echo e($r->name); ?>" value="<?php echo e($r->time); ?>" placeholder="Timer">
								</div>
								<div class="col-lg-3">
									<label>Minimum bet amount:</label>
									<input type="text" class="form-control" name="min_<?php echo e($r->name); ?>" value="<?php echo e($r->min); ?>" placeholder="Minimum bet amount">
								</div>
								<div class="col-lg-3">
									<label>Maximum bet amount:</label>
									<input type="text" class="form-control" name="max_<?php echo e($r->name); ?>" value="<?php echo e($r->max); ?>" placeholder="Maximum bet amount">
								</div>
								<div class="col-lg-3">
									<label>Maximum number of bets per player:</label>
									<input type="text" class="form-control" name="bets_<?php echo e($r->name); ?>" value="<?php echo e($r->bets); ?>" placeholder="Maximum number of bets per player">
								</div>
							</div>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<div class="tab-pane" id="wheel" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Timer:</label>
								<input type="text" class="form-control" placeholder="Timer" value="<?php echo e($settings->wheel_timer); ?>" name="wheel_timer">
							</div>
							<div class="col-lg-4">
								<label>Minimum bet amount:</label>
								<input type="text" class="form-control" placeholder="Minimum bet amount" value="<?php echo e($settings->wheel_min_bet); ?>" name="wheel_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Maximum bet amount:</label>
								<input type="text" class="form-control" placeholder="Maximum bet amount" value="<?php echo e($settings->wheel_max_bet); ?>" name="wheel_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="crash" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Timer:</label>
								<input type="text" class="form-control" placeholder="Timer" value="<?php echo e($settings->crash_timer); ?>" name="crash_timer">
							</div>
							<div class="col-lg-4">
								<label>Minimum bet amount:</label>
								<input type="text" class="form-control" placeholder="Minimum bet amount" value="<?php echo e($settings->crash_min_bet); ?>" name="crash_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Maximum bet amount:</label>
								<input type="text" class="form-control" placeholder="Maximum bet amount" value="<?php echo e($settings->crash_max_bet); ?>" name="crash_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="pvp" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Game fee in %:</label>
								<input type="text" class="form-control" placeholder="Enter the percentage" value="<?php echo e($settings->flip_commission); ?>" name="flip_commission">
							</div>
							<div class="col-lg-4">
								<label>Minimum bet amount:</label>
								<input type="text" class="form-control" placeholder="Minimum bet amount" value="<?php echo e($settings->flip_min_bet); ?>" name="flip_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Maximum bet amount:</label>
								<input type="text" class="form-control" placeholder="Maximum bet amount" value="<?php echo e($settings->flip_max_bet); ?>" name="flip_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="battle" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-3">
								<label>Timer:</label>
								<input type="text" class="form-control" placeholder="Timer" value="<?php echo e($settings->battle_timer); ?>" name="battle_timer">
							</div>
							<div class="col-lg-3">
								<label>Minimum bet amount:</label>
								<input type="text" class="form-control" placeholder="Minimum bet amount" value="<?php echo e($settings->battle_min_bet); ?>" name="battle_min_bet">
							</div>
							<div class="col-lg-3">
								<label>Maximum bet amount:</label>
								<input type="text" class="form-control" placeholder="Maximum bet amount" value="<?php echo e($settings->battle_max_bet); ?>" name="battle_max_bet">
							</div>
							<div class="col-lg-3">
								<label>Game fee in %:</label>
								<input type="text" class="form-control" placeholder="Game fee in %" value="<?php echo e($settings->battle_commission); ?>" name="battle_commission">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="dice" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Minimum bet amount:</label>
								<input type="text" class="form-control" placeholder="Minimum bet amount" value="<?php echo e($settings->dice_min_bet); ?>" name="dice_min_bet">
							</div>
							<div class="col-lg-6">
								<label>Maximum bet amount:</label>
								<input type="text" class="form-control" placeholder="Maximum bet amount" value="<?php echo e($settings->dice_max_bet); ?>" name="dice_max_bet">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet__foot">
				<div class="kt-form__actions">
					<button type="submit" class="btn btn-primary">Save</button>
					<button type="reset" class="btn btn-secondary">Reset</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/admin/settings.blade.php */ ?>