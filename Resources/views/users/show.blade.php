@extends('jplatformui::layouts.withsidebar')
@section('pageTitle')
    {{isset($pageTitle) ? $pageTitle : "Perfil - ".$user->name}}
@endsection
@section('styles')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{$user->getAvatarImageUrl()}}" alt="{{$user->name}}">
                    <h3 class="profile-username text-center">{{$user->name}}</h3>
                    <p class="text-center">
                        @if($user->active)
                            <span class="label label-success">Activo</span>
                        @else
                            <span class="label label-danger">Inactivo</span>
                        @endif
                    </p>
                    <ul class="list-group list-group-unbordered">

                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Información de perfil</h3>
                </div>
                <ul class="list-group list-group-unbordered box-body">
                    <li class="list-group-item">
                        <strong>Nombre: </strong>{{$user->name}}
                    </li>
                    <li class="list-group-item">
                        <strong>Email: </strong>{{$user->email}}
                    </li>
                    <li class="list-group-item">
                        <strong>Último ingreso: </strong>{{$user->getLastLogin()}}
                    </li>
                    @foreach($fields as $field)
                        <li class="list-group-item">
                            <strong>{{$field->fieldName}}: </strong> {!! $field->presentFront() !!}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        {!! $widgets !!}
    </div>
@endsection