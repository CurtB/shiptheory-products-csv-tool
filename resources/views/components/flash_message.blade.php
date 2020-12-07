@if ($message = Session::get('success'))

<div class="container my-4">
    <div class="alert alert-success alert-block">

        <button type="button" class="close" data-dismiss="alert">×</button>

            <strong>{!! $message !!}</strong>

    </div>
</div>

@endif


@if ($message = Session::get('error'))

<div class="container my-4">
    <div class="alert alert-danger alert-block my-4">

        <button type="button" class="close" data-dismiss="alert">×</button>

            {!! $message !!}

    </div>
</div>

@endif


@if ($message = Session::get('warning'))

<div class="container my-4">
    <div class="alert alert-warning alert-block my-4">

        <button type="button" class="close" data-dismiss="alert">×</button>

        <strong>{!! $message !!}</strong>

    </div>
</div>

@endif


@if ($message = Session::get('info'))

<div class="container my-4">
    <div class="alert alert-info alert-block my-4">

        <button type="button" class="close" data-dismiss="alert">×</button>

        <strong>{!! $message !!}</strong>

    </div>
</div>

@endif


@if ($errors->any())

<div class="container my-4">
    <div class="alert alert-danger my-4">

        <button type="button" class="close" data-dismiss="alert">×</button>

        Please check the form below for errors
        @foreach($errors->getMessages() as $validationErrors)
            @foreach($validationErrors as $error)
                <br />- {{ $error }}
            @endforeach
        @endforeach
    </div>
</div>

@endif
