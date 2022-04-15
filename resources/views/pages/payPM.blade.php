@extends('layout')

@section('content')
<form action="https://perfectmoney.is/api/step1.asp" method="POST" id="payment_form">
	<input type="hidden" name="PAYEE_ACCOUNT" value="{{$res['PAYEE_ACCOUNT']}}"/>
	<input type="hidden" name="PAYEE_NAME" value="{{$res['PAYEE_NAME']}}"/>
	<input type="hidden" name="PAYMENT_ID" value="{{$res['PAYMENT_ID']}}"/>
	<input type="hidden" name="PAYMENT_AMOUNT" value="{{$res['PAYMENT_AMOUNT']}}"/>
	<input type="hidden" name="PAYMENT_UNITS" value="{{$res['PAYMENT_UNITS']}}"/>
	<input type="hidden" name="STATUS_URL" value="{{$res['STATUS_URL']}}"/>
	<input type="hidden" name="PAYMENT_URL" value="{{$res['PAYMENT_URL']}}"/>
	<input type="hidden" name="PAYMENT_URL_METHOD" value="{{$res['PAYMENT_URL_METHOD']}}"/>
	<input type="hidden" name="NOPAYMENT_URL" value="{{$res['NOPAYMENT_URL']}}"/>
	<input type="hidden" name="NOPAYMENT_URL_METHOD" value="{{$res['NOPAYMENT_URL_METHOD']}}"/>
	<input type="hidden" name="SUGGESTED_MEMO" value="{{$res['SUGGESTED_MEMO']}}"/>
	<input type="hidden" name="BAGGAGE_FIELDS" value="{{$res['BAGGAGE_FIELDS']}}"/>
</form>
<script>
    document.getElementById("payment_form").submit();
</script>
@endsection