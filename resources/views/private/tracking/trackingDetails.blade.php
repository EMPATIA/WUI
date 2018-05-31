@extends('private._private.index')
@section('head')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    @endsection
@section('content')


    <h3>Detalhes</h3>
    <h5><b>Utilizador:</b>

        @if($trackingData->user_key==null)
              Anónimo
        @else {{$trackingData->user_key}}
        @endif
    </h5>

    <h5><b>IP:</b> {{$trackingData->ip}}</h5>
    <h5><b>Url:</b> {{$trackingData->url}}</h5>
    <h5><b>Site Key:</b> {{$trackingData->site_key}}</h5>
    <h5><b>ID de Sessão:</b> {{$trackingData->session_id}}</h5>
    <h5><b>Table Key:</b> {{$trackingData->table_key}}</h5>
    <h5><b>Tempo:</b> {{$trackingData->time}}</h5>
    <h5><b>Metodo:</b> {{$trackingData->method}}</h5>
    @if($trackingData->method=="ERROR_MSG")
        <h5><b>Mensagem de erro:</b> {{$trackingData->message}}</h5>
    @endif
    <br> <br>
    <h3>Tracking de pedidos</h3>
    @if($requests == null)
        <h5> Não desencadeou pedidos.</h5>
    @else
        @foreach($requests as $request)
    <div class="container">
        <div class="">
            <div class="card">
                <div class="card-header card-header-gray">
                    <a data-toggle="collapse" href="#{{$request->id}}">Send to {{$request->name}}</a>
                </div>
                <div id="{{$request->id}}" class="panel-collapse collapse">
                    <div class="card-footer">Resultado</div>
                    <div class="card-body">{{$request->result}}</div>
                    <div class="card-footer">Tempo</div>
                    <div class="card-body"> {{$request->time_end - $request->time_start}}</div>
                   @if($request->message!=null)
                        <div class="card-footer">Mensagem</div>
                        <div class="card-body"> {{$request->message}}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
     </h4>
    @endif
@endsection
