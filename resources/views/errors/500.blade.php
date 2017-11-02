@extends("errors.base")

@section("pageTitle")
    <title>HTTP 500 - EMPATIA</title>
@endsection

@section("image")
    <img src="/images/errors/server_error.png" class="d-block">
@endsection

@section("icon")
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
@endsection

@section("title")
    Internal Server Error
@endsection

@section("subtitle")
    HTTP 500
@endsection

@section("description")
    The server encountered and internal error or misconfiguration and was unable to complete your request
@endsection

@section("button")
    <a href="{{ url()->previous() }}" class="btn-goBack">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
        Go back
    </a>
@endsection

