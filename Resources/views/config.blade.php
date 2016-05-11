@extends('jplatformui::layouts.withsidebar')
@section('pageTitle')
    {{isset($pageTitle) ? $pageTitle : "Configuración"}}
@endsection
@section('styles')

@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Configuración del módulo de usuarios</h3>
                </div>
                <div class="box-body">
                    @if(Auth::user()->ability('administrador-del-sistema', 'user-configuration'))
                        <a href="{{ route('users.config') }}" class="btn btn-app">
                            <i class="fa fa-users"></i> Campos de perfil
                        </a>
                    @endif
                    @if(Auth::user()->ability('administrador-del-sistema', 'create-role,edit-role,delete-role,admin-permissions'))
                        <a href="{{ route('roles.index') }}" class="btn btn-app">
                            <i class="fa fa-key"></i> Roles y Permisos
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection