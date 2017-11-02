@extends("errors.base")

@section("pageTitle")
    <title>HTTP 402 - EMPATIA</title>
@endsection

@section("image")
    <img src="/images/errors/unauthorized.png" class="d-block">
@endsection

@section("icon")
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
@endsection

@section("title")
    Forbidden
@endsection

@section("subtitle")
    HTTP 402
@endsection

@section("description")
    You have attempted to access a page that you are not authorized to view.<br>
    If you have any questions, please contact the site Administration
@endsection

@section("button")
    <a href="{{ url()->previous() }}" class="btn-goBack">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
        Go back
    </a>
@endsection

