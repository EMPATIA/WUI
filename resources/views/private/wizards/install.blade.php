@extends('private.wizards._layout')
@section('header_styles')
    <link rel="stylesheet" href="/fonts/fontello-bc7d3037/css/fontello.css">
@endsection
@section('content')
    <style>
        .icon-size{
            font-size: 55px;
        }

        .my-row{
            display: flex;
            flex-wrap: wrap;
        }

        .my-col{
            padding:15px;
            flex:1;
            display: flex;
            flex-direction: column;
            flex-basis: 100%;
            max-width: 100%;
        }

        @media (min-width: 768px){
            .my-col{
                flex-basis: 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 992px){
            .my-col{
                flex-basis: 33.3%;
                max-width: 33.3%;
            }
        }

        .my-col>a{
            flex: 1;
            border: 2px solid #3b8ab8;
        }

        .my-col>a:hover,
        .my-col>a:hover > div{
            background-color: #3b8ab8;
            color: #fff;
            cursor: pointer;
        }

        .my-col>a:hover .btn-presentation{
            background-color: transparent;
        }

        .btn-presentation{
            border: none;
        }

        .btn-presentation:hover{
            border:none;
            background-color: transparent;
        }


        .page-title{
            padding: 15px;
            margin-bottom: 30px;;

        }

        .page-title>h2{
            margin: 0;
        }

        .btn-grid > div > a{
            display: flex;
        }
        .btn-presentation{
            padding: 5px;
        }
        .btn-back:hover{
            background: #3b8ab8;
            color: white;
        }
        .btn-back{
            background: white;
            color: #3b8ab8;
        }
        .my-row > .my-col > a.disabled{
            pointer-events: none;
            background: #f5f5f5;
        }
        .my-row > .my-col > a.disabled > .btn-presentation {
            background: #f5f5f5;
        }
    </style>
    @php
        $entityName = Session::get("firstInstallWizardEntityName","");
        $cbName = Session::get("firstInstallWizardCBName","");
    @endphp
    <div class="box-buffer-rap">
        <div class="page-title text-center">
            <h1>{{ trans("privateFirstInstall.title") }}</h1>
        </div>
        <div class="my-row">
            <div class="my-col">
                <a href="{{ action('EntitiesController@createWizard') }}" class="text-center @if(!empty($entityName)) disabled @endif">
                    <div class="btn-presentation flex-container">
                        <div class="wrap-content">
                            <i class="demo-icon icon-wizard-icon_entity_path icon-size"></i>
                            <div class="" >
                                {{ trans("privateFirstInstall.create_entity") }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col">
                <a href="{{ action('CbsController@createWizard') }}" class="text-center @if(!empty($cbName) || empty($entityName)) disabled @endif">
                    <div class="btn-presentation flex-container">
                        <div class="wrap-content">
                            <i class="demo-icon icon-wizard-icon_participation_path icon-size"></i>
                            <div style="line-height: normal">
                                {{ trans("privateFirstInstall.create_cb")}}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="my-col">
                <a href="{{ action('QuickAccessController@firstInstallWizardFinish') }}" class="text-center @if(empty($entityName) || empty($cbName)) disabled @endif">
                    <div class="btn-presentation flex-container">
                        <div class="wrap-content">
                            <i class="demo-icon icon-wizard-icon_dahboard_path icon-size"></i>
                            <div style="line-height: normal">
                                {{ trans("privateFirstInstall.go_to_dashboard")}}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-12">
                <div class="background">
                    <a href="{{ action('AuthController@adminLogin') }}" style="border: 2px solid"  class="btn btn-back">
                        <i class="fa fa-times" aria-hidden="true">
                            <span style="padding-left: 10px">{{trans("privateCbsWizard.cancel")}}</span>
                        </i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection