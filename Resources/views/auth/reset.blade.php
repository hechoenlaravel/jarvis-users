@extends('jplatformui::layouts.main')
@section('pageTitle')
    Restarurar Contraseña
@endsection
@section('content')
    <div class="container">
        <div class="login-box">
            <div class="login-logo">

            </div>
            <div class="login-box-body">
                {!! Form::open(['route' => 'reset-password']) !!}
                <input type="hidden" name="token" value="{{ $token }}">
                {!! Field::email('email', ['label' => 'Usuario']) !!}
                {!! Field::password('password', ['label' => 'Contraseña']) !!}
                {!! Field::password('password_confirmation', ['label' => 'Confirme su contraseña']) !!}
                {!! Form::submit('Ingresar', ['class' => 'btn btn-lg btn-primary btn-block']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
