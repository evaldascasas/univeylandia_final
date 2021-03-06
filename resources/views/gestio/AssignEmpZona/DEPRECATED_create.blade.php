@extends("layouts.gestio")

@section("navbarIntranet")
@endsection
@section("menuIntranet")
@endsection
@section("content")

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2>Assignar Empleat a Zona</h2>
</div>

@if ($errors->any())
        <div class="alert alert-danger alert-important">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

<div class="row">
    <div class="col-md-12 px-4">
        <h5>Selecciona la Zona a assignar</h5>
    </div>
    <form class="needs-validation" method="post" action="{{ route('AssignEmpZona.store') }}">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($zones as $zona)
                <tr>
                    <td>{{ $zona->id }}</td>
                    <td>{{ $zona->nom }}</td>
                    <td><input type="radio" class="form-check-input" name="seleccio_zona" value="{{ $zona->id }}"></td>
                </tr>
                @endforeach
            </tbody>


        </table>
        <br />
        <h5>Selecciona la data d'inici</h5>
        <input class="form-control" type="date" name="data_inici_assign">
        <br />
        <h5>Selecciona la data límit</h5>
        <input class="form-control" type="date" name="data_fi_assign">
        <br />
        <br />
</div>


<div class="col-md-12 px-4">
    <h5>Selecciona l'Empleat a assignar</h5>
</div>

<table class="table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nom</th>
            <th scope="col">Cog 1</th>
            <th scope="col">Cog 2</th>
            <th scope="col">DNI</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($treballadors as $treballador)
        <tr>
            <td>{{ $treballador->id }}</td>
            <td>{{ $treballador->nom }}</td>
            <td>{{ $treballador->cognom1 }}</td>
            <td>{{ $treballador->cognom2 }}</td>
            <td>{{ $treballador->numero_document }}</td>
            <td><input type="checkbox" class="form-check-input" name="seleccio_empleat" value="{{ $treballador->id }}">
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<button class="btn btn-primary" type="submit" value="Crear assignació">Crear Assignacio</button>
</form>

@endsection