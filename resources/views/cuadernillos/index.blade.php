@extends('voyager::master')

@section('page_title','Cuadernillos')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-file-text"></i>
            @yield("page_title","Cuadernillos")
        </h1>
    </div>
@stop

@section('content')
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <h5 class="panel-title"><i class="voyager-people"></i> Listado de cuadernillos</h5>
            </div>
            <div class="panel-body">
                <booklet-table-component :url="'{{ route('voyager.cuadernillos.data') }}'"></booklet-table-component>
            </div>
        </div>
    </div>
@stop