@extends("layouts.gestio")

@section("navbarIntranet")
@endsection
@section("menuIntranet")
@endsection
@section("content")


<div class="card uper">
  <div class="card-header">
    Editar Zona: {{ $zona->nom }}
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

  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div>
    @endif
      <form method="post" action="{{ route('zones.update', $zona->id) }}">
        @method('PATCH')
        @csrf
        <div class="form-group">
          <label for="nom">Zona Nom:</label>
          <input type="text" class="form-control" name="zona_nom" value="{{ $zona->nom }}">
        </div>
        <button type="submit" class="btn btn-primary" value="Actualitzar">Actualitza</button>
        <a href="{{ URL::previous() }}" class="btn btn-secondary">Cancel·lar</a>
      </form>
  </div>
</div>
@endsection
