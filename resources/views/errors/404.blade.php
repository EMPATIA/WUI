@extends("errors.base")

@section("pageTitle")
    <title>HTTP 404 - EMPATIA</title>
@endsection

@section("image")
    <img src="/images/errors/generic.png" class="d-block">
@endsection

@section("icon")
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
@endsection

@section("title")
    Page not found
@endsection

@section("subtitle")
    HTTP 404
@endsection

@section("description")
    Sorry, this page does not exist, the link you followed probably is broken, or the page has been removed
@endsection

@section("button")
    <a href="{{ url()->previous() }}" class="btn-goBack">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
        Go back
    </a>
@endsection

