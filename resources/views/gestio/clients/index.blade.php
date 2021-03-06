@extends("layouts.gestio")

@section("navbarIntranet")
@endsection
@section("menuIntranet")
@endsection
@section("content")

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ __('Clients') }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group mr-2">
            <div class="btn-group dropleft">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span data-feather="save"></span>
                    {{ __('Exportar') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{{ route('clients.pdf') }}">{{ __('PDF') }}</a>
                    <a class="dropdown-item" href="{{ route('clients.csv') }}">{{ __('CSV') }}</a>                  
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table
        class="table table-bordered table-hover table-sm dt-responsive nowrap dataTable no-footer dtr-inline collapsed"
        id="results_table" role="grid">
        <thead class="thead-light">
            <tr>
                <th>{{ __('Nom') }}</th>
                <th>{{ __('1r Cognom') }}</th>
                <th>{{ __('2n Cognom') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Data naixement') }}</th>
                <th>{{ __('Adreça') }}</th>
                <th>{{ __('Ciutat') }}</th>
                <th>{{ __('Provincia') }}</th>
                <th>{{ __('CP') }}</th>
                <th>{{ __('Tipus document') }}</th>
                <th>{{ __('Número document') }}</th>
                <th>{{ __('Sexe') }}</th>
                <th>{{ __('Telèfon') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            @foreach ($usuaris as $usuari)
            <tr>
                <td>{{$usuari->nom}}</td>
                <td>{{$usuari->cognom1}}</td>
                <td>{{$usuari->cognom2}}</td>
                <td>{{$usuari->email}}</td>
                <td>{{$usuari->data_naixement}}</td>
                <td>{{$usuari->adreca}}</td>
                <td>{{$usuari->ciutat}}</td>
                <td>{{$usuari->provincia}}</td>
                <td>{{$usuari->codi_postal}}</td>
                <td>{{$usuari->tipus_document}}</td>
                <td>{{$usuari->numero_document}}</td>
                <td>{{$usuari->sexe}}</td>
                <td>{{$usuari->telefon}}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Accions">
                        <a href="{{ route('clients.show', $usuari->id) }}"
                            class="btn btn-outline-success">{{ __('Mostrar') }}</a>
                        <a href="{{route('clients.edit', $usuari->id)}}"
                            class="btn btn-outline-primary btn-sm">{{ __('Modificar') }}</a>
                        <form method="post" action="{{route('clients.destroy', $usuari->id)}}">
                            @csrf
                            @method('DELETE')
                            <button id="confirm_delete" class="btn btn-outline-danger btn-sm" type="submit"
                                value="Desactivar">{{ __('Desactivar') }}</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection