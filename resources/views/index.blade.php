@extends("layouts.plantilla")

@section("menu1")
@endsection
@section("menu2")
@endsection
@section("content")
<!-- SLIDER-->
<div id="carousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="d-block w-100" src="/img/slider1.jpg" alt="imatge del parc">
            <div class="carousel-caption">
                <h2 class="text-center message"> Bevingut al parc dels teus somnis!</h2>
            </div>
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="/img/slider2.jpg" alt="imatge del parc">
            <div class="carousel-caption">
                <h2 class="text-center message"> Bevingut al parc dels teus somnis!</h2>
            </div>
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="/img/slider3.jpg" alt="imatge del parc">
            <div class="carousel-caption">
                <h2 class="text-center message"> Bevingut al parc dels teus somnis!</h2>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Prèvia</span>
    </a>
    <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Següent</span>
    </a>
</div>
<!-- FI SLIDER -->

<!-- PROMOCIONS -->
<div class="container mt-3">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="font-weight-bold text-center">PROMOCIONS NADAL 2019</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <a href="{{route('promocions')}}"><img src="/img/promocions/promocio1.jpg" class="img-fluid"
                    alt="imatge promoció 1"></a>
        </div>
    </div>
</div>
<!-- FI PROMOCIONS -->

<!-- NOTICIES -->
<div class="container mt-3">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="font-weight-bold text-center text-uppercase">noticies</h1>
        </div>
    </div>
    <div class="row">
        @forelse($noticies as $noticia)
        <div class="col-sm-6">
            <div class="card flex-md-row mb-4 box-shadow h-md-250">
                <img class="card-img-top" alt="imatge de la noticia"
                    style="width: 200px;height: 300px; object-fit: cover;" src="{{$noticia->path_img}}">

                <div class="card-body d-flex flex-column align-items-start">
                    <a href="/noticies?catId={{$noticia->catId}}" class="d-inline-block mb-2 text-success"
                        style="background: none;border: none;"> {{$noticia->categoria}}</a>
                    <h3 class="mb-0">
                        <a class="text-dark">{{$noticia->titol}}</a>
                    </h3>
                    <p class="card-text mb-auto">{!!html_entity_decode(str_limit($noticia->descripcio, $limit=100, $end
                        = "..."))!!}</p>
                    <form action="{{ route('noticia',$noticia->id)}}" method="get">
                        <input type="hidden" name="id" value="{{$noticia->id}}">
                        <button type="submit" class="btn btn-outline-info">Continuar llegint</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p style="background-color: #e05e5e;text-align: center;font-weight: bold;"> No hi han noticies a llistar</p>
        @endforelse
    </div>
    <!-- FI NOTICIES -->

    <!-- LOCALITZA -->
    <div class="container mt-3">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="font-weight-bold text-center">ON ESTEM?</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <iframe title="Localitzacio del parc"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.4047935586386!2d0.5816534201918497!3d40.70910459358563!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12a0fe735557799b%3A0x3fade49ba3687306!2sInstitut+Montsi%C3%A0+i+CFA+Sebasti%C3%A0+Juan+Arb%C3%B3!5e0!3m2!1sca!2ses!4v1553034764770"
                    width="100%" height="200px" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <!-- FI LOCALITZA -->
</div>

<div class="container mt-3">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="font-weight-bold text-center">On estem 2</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
            <script
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAc4rbBZW_EiNrWWjzcgb2NnFAeBD66cSs&callback=myMap"></script>
            <script>
                $(document).ready(function () {
                    var marker;
                    var geocoder = new google.maps.Geocoder();
                    var myLatlng = new google.maps.LatLng(40.7160476, 0.5648026);
                    var mapOptions = { zoom: 7, center: myLatlng, mapTypeId: google.maps.MapTypeId.ROADMAP }
                    var map = new google.maps.Map($("#map").get(0), mapOptions);

                    $("#links a").click(function () {
                        var address = $(this).text();
                        if (marker) { marker.setMap(null); }
                        geocoder.geocode({ address: address }, function (results) {
                            marker = new google.maps.Marker({
                                position: results[0].geometry.location, map: map
                            });
                            var overlay = new google.maps.OverlayView();
                            overlay.draw = function () {
                                var point = overlay.getProjection().fromLatLngToContainerPixel(
                                    marker.getPosition());
                                $("#message").html(
                                    "This is: " + address +
                                    "<br><a href=http://maps.google.com/maps?daddr=" +
                                    address + ">Get directions to here</a>");
                                $("#message").show().css({
                                    top: point.y + 10,
                                    left: point.x
                                });
                            };
                            overlay.setMap(map);
                        });
                    });
                });
            </script>

            <div id="map" style="width:100%;height:400px;"></div>
            <ul id="links">
                <li><a href="#">Univeylandia</a></li>
                <li><a href="#">IES Montsia</a></li>
            </ul>
            <div id="message" style="display:none;"></div>
        </div>
    </div>
</div>
<!-- FI LOCALITZA -->
</div>

<div class="container mt-3">
    <div class="row">
        <div class="col-sm-12">
            <h1 class="font-weight-bold text-center">Fotos realitzades pels nostres clients</h1>
        </div>
    </div>
    <div class="row" id="photos">
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "https://api.flickr.com/services/feeds/photos_public.gne?" +
            "format=json&jsoncallback=?&tags=parc+atraccions";

        $.getJSON(url, function (data) {
            var html = "";
            $.each(data.items, function (i, item) {
                if (i <= 11) {
                    html += "<div class='col-lg-2 col-md-4 col-6'>"
                    html += "<a class='d-block mb-4' href=" + item.link + ">";
                    html += "<img style='width:150px; height: 150px;' class='img-fluid img-thumbnail' src=" + item.media.m + ">";
                    html += "</a>";
                    html += "</div>";
                    html = html.replace("/>", ">");
                }
            });
            $("#photos").html(html);
        });
    });
</script>

@endsection

@section("footer")
@endsection