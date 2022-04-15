@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/faq.css">
<div class="section">
    <div class="faq-component">
        <div class="faq-head">
            <h1 class="faq-caption">Response to question</h1>
            @if($settings->vk_support_link)
            <div class="faq-link"><a class="btn btn-light" href="{{$settings->vk_support_link}}" target="_blank">Write to support</a></div>
            @endif
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-faq">
                        <use xlink:href="/img/symbols.svg#icon-faq"></use>
                    </svg> About the site
                </div>
            </div>
            <div class="faq-content">
                <p>{{$settings->sitename}} - this is an exciting and provable private mini-games.</p>
                <p>Play games and win coins that you can exchange for $.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-coin">
                        <use xlink:href="/img/symbols.svg#icon-coin"></use>
                    </svg> Coins
                </div>
            </div>
            <div class="faq-content">
                <p>Coins are our in-game currency. Rate: 0.01 COIN = 0.01$</p>
                <p>You can buy coins on the page <a class="" data-toggle="modal" data-target="#walletModal">coin purchases</a> or get free every 15 minutes, on the page <a class="" href="/free">free coins</a></p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-fairness">
                        <use xlink:href="/img/symbols.svg#icon-fairness"></use>
                    </svg> Fair game
                </div>
            </div>
            <div class="faq-content">
                <p>The random number generator creates provable and completely honest random numbers that are used to determine the result of each game played on the site.</p>
                <p>Each user can check the outcome of any game in a completely deterministic way. Providing a single parameter - the client cache, the inputs of the random number generator, {{$settings->sitename}} can't manipulate the results to his advantage.</p>
                <p>Random number generator {{$settings->sitename}} allows each game to request any number of random numbers from a given client seed, server seed, and one-time number.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-affiliate">
                        <use xlink:href="/img/symbols.svg#icon-affiliate"></use>
                    </svg> Affiliate program
                </div>
            </div>
            <div class="faq-content">
                <p>Invite other players to our website at <a class="" href="/affiliate">your referral link</a> and earn {{$settings->ref_perc}}% of our profits with every bet made by your referral.</p>
            </div>
        </div>
    </div>
</div>
@endsection