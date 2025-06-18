@extends('layouts.app')

@section('title', 'Crear hoja de firmas')

@section('title-icon', 'fas fa-signature')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('secretary.meeting.manage') }}">Gestionar reuniones</a></li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')

    <div class="row">

        <x-menumeeting />

        <div class="col-md-8">

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-signature me-2"></i> Crear hoja de firmas para una reunión</h5>
                </div>

                <div class="card-body">

                    <div class="alert alert-info">
                        <p>Puedes crear una hoja de firma virtual para una reunión.</p>
                        <p>Esto generará una URL del tipo <u>evidentia.us.es/sign/<b>XXXX</b></u>.</p>
                        <p>Con esta URL, los asistentes podrán firmar con su usuario y contraseña y quedarán registrados.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('secretary.meeting.manage.signaturesheet.new') }}"
                        id="request_form">
                        @csrf

                        <x-id :id="$signature_sheet->id ?? ''" :edit="$edit ?? ''" />

                        <div class="form-row">

                            <x-input col="6" attr="title" :value="$signature_sheet->title ?? ''" label="Título"
                                description="Escribe un título para tu hoja de firmas" />

                            <div class="form-group col-md-6">
                                <label for="meeting_request">Convocatoria asociada</label>
                                <select id="meeting_request"
                                    class="selectpicker form-control @error('meeting_request') is-invalid @enderror"
                                    name="meeting_request">

                                    <option value="">Sin convocatoria asociada</option>

                                    @foreach ($available_meeting_requests as $meeting_request)
                                        <option value="{{ $meeting_request->id }}"
                                            {{ old('meeting_request') == $meeting_request->id ? 'selected' : '' }}>
                                            {!! $meeting_request->title !!}
                                        </option>
                                    @endforeach
                                </select>

                                <small class="form-text text-muted">
                                    Elige una convocatoria a la que quieras asociar tu hoja de firmas.
                                    Solo puedes asociar convocatorias que <b>no tengan</b> una hoja de firmas.
                                </small>

                                @error('meeting_request')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="form-group col-md-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-signature"></i>&nbsp;Crear hoja de firmas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>

@section('scripts')

    <script id="point_template" type="text/x-handlebars-template">

            <li class="rounded" style="" id="@{{id}}">

                <span class="handle ui-sortable-handle">
                  <i class="fas fa-ellipsis-v"></i>
                  <i class="fas fa-ellipsis-v"></i>
                </span>

                <span class="text point_title">@{{title}}</span>

                <div class="tools">
                    <i class="fas fa-trash" onclick="remove(@{{id}})"></i>
                </div>
            </li>

        </script>

    <script>
        $(document).ready(function() {

            var form = $("#request_form");

            form.submit(function(event) {

                // añadimos los puntos al envío
                var points = [];
                $(".point_title").each(function() {
                    points.push($(this).html())
                    $("#points_list").val(JSON.stringify(points));
                });

                return true;
            });

            $("#add_point").keypress(function(e) {

                var code = (e.keyCode ? e.keyCode : e.which);

                if (code == 13) {
                    e.preventDefault();
                    add_point();
                    return false;
                }
            });

        });

        function add_point() {
            var source = document.getElementById("point_template").innerHTML;
            var template = Handlebars.compile(source);
            var context = {
                title: $("#add_point").val(),
                id: make_id(16)
            };
            var html = template(context);
            $("#points").append(html);
            $("#add_point").val("");
        }

        $("#button_add_point").click(function() {
            add_point();
        })

        function remove(id) {
            $("#" + id).remove();
        }
    </script>

@endsection


@endsection
