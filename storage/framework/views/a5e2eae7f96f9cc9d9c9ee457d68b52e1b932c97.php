

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="/css/affiliate.css">
<div class="section">
    <div class="section-page">
        <div class="quest-banner affiliate">
            <div class="caption">
                <h1><span>Referral program</span></h1>
			</div>
            <div class="info"><span>Earn <?php echo e($settings->ref_perc); ?>% from your referral's win amount.</span></div>
            <div class="info"><span>Your referrals receive <?php echo e($settings->ref_sum); ?> bonuses at registration!</span></div>
        </div>
        <div class="affiliates-form">
            <div class="text">Your link to attract referrals:</div>
            <form>
                <div class="form-row">
                    <div class="form-field input-group">
                        <div class="input-valid">
                            <input class="input-field" type="text" name="code" id="code" readonly="" value="<?php echo e(strtolower($_SERVER['REQUEST_SCHEME']).'://'); ?><?php echo e(strtolower($settings->domain)); ?>/?ref=<?php echo e($u->unique_id); ?>">
                            <div class="input-group-append">
                                <button type="button" class="btn" onclick="copyToClipboard('#code')"><span>Copy</span></button>
                                <div class="copy-tooltip"><span>Copied</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="affiliate-stats">
            <div class="left">
                <div class="affiliate-stats-item">
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-coin bonus">
                                <use xlink:href="/img/symbols.svg#icon-coin"></use>
                            </svg>
                            <div class="num"><?php echo e($u->ref_money_all); ?></div>
                            <div class="text">Total income</div>
                        </div>
                    </div>
                </div>
                <div class="affiliate-stats-item border-top">
                    <div class="wrap border-right">
                        <div class="block">
                            <svg class="icon icon-network">
                                <use xlink:href="/img/symbols.svg#icon-network"></use>
                            </svg>
                            <div class="num"><?php echo e($u->link_trans); ?></div>
                            <div class="text">Transitions</div>
                        </div>
                    </div>
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-person">
                                <use xlink:href="/img/symbols.svg#icon-person"></use>
                            </svg>
                            <div class="num"><?php echo e($u->link_reg); ?></div>
                            <div class="text">Registrations</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="affiliate-stats-item full">
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-coin bonus">
                                <use xlink:href="/img/symbols.svg#icon-coin"></use>
                            </svg>
                            <div class="num"><?php echo e($u->ref_money); ?></div>
                            <div class="text">Available balance</div>
                            <span id="withdraw-button" class="" data-toggle="tooltip" data-placement="top" title="Minimum withdrawal amount <?php echo e($settings->min_ref_withdraw); ?> coins"><button type="button" class="btn" <?php echo e($u->ref_money < $settings->min_ref_withdraw  ? 'disabled' : ''); ?>>Take</button></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/pages/affiliate.blade.php */ ?>