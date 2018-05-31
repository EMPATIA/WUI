@extends("errors.base")

@section("pageTitle")
    <title>EMPATIA - Unauthorized</title>
@endsection

@section("image")
    <img src="/images/errors/unauthorized.png" class="d-block">
@endsection

@section("icon")
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
@endsection

@section("title")
    Unauthorized
@endsection

@section("subtitle")
    EMPATIA
@endsection

@section("description")
    You have attempted to access an non authorized Site.<br>
    If you have any questions, please contact the site Administration.
@endsection