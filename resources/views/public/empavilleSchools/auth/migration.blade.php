@extends('public.empavilleSchools._layouts.index')

@section('content')
    <div class="row top-4 blue margin-0 text-center">
        <div class="container">
            <div class="col-md-12 bottom-3">
                <h2 class="title"><a href="#" class="h1-link">{{ trans("auth.user_needs_entity_migration_title") }}</a></h2>
                <div class="subtitle">{{ trans("auth.user_needs_entity_migration_content") }}</div>
            </div>
        </div>
    </div>
    <div class="row pad-4 margin-0 gray">
        <form action="{{ URL::action('AuthController@migrateUserToEntity') }}" method="POST">
            <div class="text-center">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" name="response" value="1" class="btn btn-danger btn-md">{{ trans('auth.user_needs_entity_migration_accept') }}</button>
                <button type="submit" name="response" value="0" class="btn btn-primary btn-md">{{ trans('auth.user_needs_entity_migration_decline') }}</button>
            </div>
        </form>
    </div>
@endsection