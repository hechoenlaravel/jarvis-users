@extends('jplatformui::layouts.main')
@section('pageTitle')
    Ingreso
@endsection
@section('content')
    <div class="container" ng-controller="LoginController">
        <div class="login-box">
            <div class="login-logo">

            </div>
            <div class="login-box-body">
                {!! Alert::render() !!}
                {!! Form::open(['route' => 'login-post']) !!}
                {!! Field::email('email', ['label' => 'Usuario']) !!}
                {!! Field::password('password', ['label' => 'Contraseña']) !!}
                {!! Form::submit('Ingresar', ['class' => 'btn btn-lg btn-primary btn-block']) !!}
                {!! Form::close() !!}
                <hr />
                <a href="#" ng-click="forgotPassword()" class="btn btn-block btn-default">Olvide mi contraseña</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('modules/users/js/login.js')}}"></script>
@endsection
