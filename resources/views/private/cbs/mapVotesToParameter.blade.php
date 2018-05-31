@extends('private._private.index')

@section('content')
    @php
        $form = ONE::form('mapVotesToParameter', trans('mapVotesToParameter.title'))
            ->settings(["options" => ['mapVotesToParameter' =>  $type, ONE::actionType('mapVotesToParameter')]])
            ->create('CbsVoteController@mapVotesToParameterSubmit', 'CbsVoteController@show', ['type' => $type,'cbKey' => $cbKey, 'voteKey' => $voteKey])
            ->open();
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-6" style="padding-top:10px;">
                @php $printedCbParameters = false; @endphp
                <select name="parameter" class="form-control" required>
                    @forelse($cbParameters as $cbParameter)
                        @if($cbParameter->code=="text_area" || $cbParameter->code=="text" || $cbParameter->code=="numeric")
                            @php $printedCbParameters = true; @endphp
                            <option value="{{ $cbParameter->id }}"
                                @if(old("description-parameter")==$cbParameter->id) selected @endif>
                                {{ $cbParameter->parameter }}
                            </option>
                        @endif
                    @empty
                    @endforelse

                    @if(!$printedCbParameters && true)
                        <option value="" disabled>
                            {{ trans("privatePublishTechnicalAnalysis.no_parameters_available") }}
                        </option>
                    @endif
                </select>
            </div>
        </div>
    </div>

    {!! $form->make() !!}
@endsection