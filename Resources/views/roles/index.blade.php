@extends('jplatformui::layouts.withsidebar')
@section('pageTitle')
    {{isset($pageTitle) ? $pageTitle : "Roles"}}
@endsection
@section('styles')

@endsection
@section('content-header')
    <h2><i class="fa fa-users"></i> Roles</h2>
    <p>Admintración de roles del sistema, piense en roles como grupos de usuarios con capacidades esfecificas para realizar acciones en el sistema.</p>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Roles
                    </h3>
                    <div class="box-tools pull-right">
                        @if(Auth::user()->hasPermissionTo('Crear roles'))
                            <a href="{{route('roles.create')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Crear rol</a>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Rol</th>
                                <th>Fecha de ultima actualización</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->updated_at->format('d/m/Y') }}</td>
                                    <td width="150">
                                        @if(Auth::user()->hasPermissionTo('Editar roles') && $role->name != "Administrador del sistema")
                                            <a href="{{ route('roles.edit', ['id' => $role->id]) }}" data-toggle="tooltip" data-placement="top" title="Editar rol" class="btn btn-sm btn-default"><i class="fa fa-pencil"></i></a>&nbsp;
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('Eliminar roles') && $role->name != "Administrador del sistema")
                                            <a href="{{ route('roles.destroy', ['id' => $role->id]) }}" data-toggle="tooltip" data-placement="top" title="Eliminar rol" class="btn btn-sm btn-danger confirm-delete"><i class="fa fa-times"></i></a>&nbsp;
                                        @endif
                                        @if(Auth::user()->hasPermissionTo('Asignación de permisos a roles') && $role->name != "Administrador del sistema")
                                            <a href="{{ route('roles.permissions', ['id' => $role->id]) }}" data-toggle="tooltip" data-placement="top" title="Editar permisos" class="btn btn-sm btn-primary"><i class="fa fa-lock"></i></a>&nbsp;
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    {!! $roles->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection