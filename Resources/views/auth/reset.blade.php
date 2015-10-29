@extends('layouts.main')
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
                {!! Field::email('email', ['label' => 'Usuario', 'placeholder' => 'admin@admin.com']) !!}
                {!! Field::password('password', ['label' => 'Contraseña', 'placeholder' => 'admin']) !!}
                {!! Field::password('password_confirmation', ['label' => 'Confirme su contraseña', 'placeholder' => 'admin']) !!}
                {!! Form::submit('Ingresar', ['class' => 'btn btn-lg btn-primary btn-block']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
