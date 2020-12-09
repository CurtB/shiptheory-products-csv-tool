<?php
    if(empty($mode)){
        $mode = Session::get('mode', 'import');
    }
?>

@extends('layout')

@section('style')

<style>

</style>

@endsection

@section('content')

<div class="container" id="main-container" style="min-height: 740px;">


@if(!empty($report))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning" role="alert">
            <a href="#" class="alert-link" id="toggle-report">
                Click to see more information <i class="fas fa-angle-down"></i>
            </a>
            </div>
            <br />
            <div id="report">
                <ul class="list-group list-group-flush">
                @foreach($report as $key => $val)
                    @if(!empty($val['error']))
                        <li class="list-group-item list-group-item-warning mb-0">
                            <strong>Row {{ $key }}:</strong> {!! $val['info'] !!}<br />
                            <p class="mb-0 pb-0">
                            @if(is_array($val['error']) || is_object($val['error']))
                                @foreach($val['error'] as $col => $issues)
                                    @foreach($issues as $k => $description)
                                        {!! $description !!}.
                                    @endforeach
                                @endforeach
                            @else
                                {!! $val['error'] !!}
                            @endif
                            </p>
                        </li>
                    @endif
                @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

    <div class="row">
        <div class="col-0 col-md-1 col-lg-2"></div>
        <div class="col-12 col-md-10 col-lg-8 upload mb-2">
            <div class="upload-box pt-4">
                <h1>Products CSV Tool</h1>
                <p>Import, export and update products in
                <a href="https://www.shiptheory.com/" target="_blank">Shiptheory</a>
                with a CSV file using the
                <a href="https://shiptheory.com/developer/index.html" target="_blank">
                    Products API</a>.
                <br>This is an unofficial tool and is offered on an as-is basis.</p>
            </div>
        </div>
    </div>

    <div class="row mt-0">
        <div class="col-0 col-md-1 col-lg-2"></div>
        <div class="col-12 col-md-10 col-lg-8 upload mt-0">
            <div class="upload-box">
                @if(!empty($authed))
                <div style="width: 100%; text-align: right;">
                    <a href="logout" target="_blank">
                        <button type='button' class='btn-danger'><i class="fas fa-times-circle mr-3 ml-0"></i> Logout
                        </button>
                    </a>
                </div>
                @endif

            {!! Form::open([
                'url' => 'import_csv',
                'id' => 'csv_form',
                'enctype' => 'multipart/form-data']) !!}
                <input id="mode" name="mode" value="{{$mode}}" type="hidden" />

                @if(empty($authed))
                    <input type='email' name='email' placeholder='Shiptheory Email'
                           required value="" />
                    <br>
                    <input type='password' name='password'
                        placeholder='Shiptheory Password'
                        required value="" />
                @endif

                <br /><br />
                What would you like to do with your products?

                <div class="btn-group btn-group-justified mt-3" style="width: 100%;">
                  <button type="button"
                    class="btn btn-@if($mode=='import')primary @endif"
                    id="import-btn">
                    Import
                  </button>
                  <button type="button"
                    class="btn btn-@if($mode=='update')primary @endif"
                    id="update-btn">
                    Update
                  </button>
                  <button type="button"
                    class="btn btn-@if($mode=='export')primary @endif"
                    id="export-btn">
                    Export
                  </button>
                </div>

                    <br>
                    <input id="file-input" type="file" name="csv_file" size="25" />

                    <p><strong>Warning:</strong> If you are trying to use large files with hundreds or thousands of products you may encounter errors along the lines of &rdquo;We encountered an error when trying to load your application...&ldquo;. To get round this it can help to break your products into smaller csv files.</p>
                    <button type="submit" class='button-primary mt-3' id="submit-btn">
                        Import CSV
                    </button>

                    <span id="patient">Please be patient, this could take a while.</span>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h5>Uploading a Products CSV</h5>
            <hr>
            <p>Line 1 of your CSV must contain the field names listed below (The order
                of fields is not important).</p>

            <h5>Sample CSV Files</h5>
            <hr>
            <p>You can download a CSV template file here: <a href="template.csv">template.csv</a>
            <br>You can download an example CSV file here: <a href="example_products.csv">example.csv</a></p>

            <h5>CSV Reference</h5>
            <hr>
            <table id="reference">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>sku</td>
                        <td>Yes</td>
                        <td>Your unique product SKU. Max 100 characters</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>No</td>
                        <td>Product name. Max 255 characters</td>
                    </tr>
                    <tr>
                        <td>price</td>
                        <td>No</td>
                        <td>Price of the product. Between 0 and 9999999.99</td>
                    </tr>
                    <tr>
                        <td>weight</td>
                        <td>No</td>
                        <td>Weight of the product in Kg. Between 0 and 9999999.99</td>
                    </tr>
                    <tr>
                        <td>barcode</td>
                        <td>No</td>
                        <td>Product barcode. Max 15 characters</td>
                    </tr>
                    <tr>
                        <td>commodity_code</td>
                        <td>No</td>
                        <td>Commodity HS Tariff code. Max 100 characters</td>
                    </tr>
                    <tr>
                        <td>commodity_description</td>
                        <td>No</td>
                        <td>Commodity HS Tariff code. Max 100 characters</td>
                    </tr>
                    <tr>
                        <td>commodity_manucountry</td>
                        <td>No</td>
                        <td>Commodity country of manufacture. Max 100 characters</td>
                    </tr>
                    <tr>
                        <td>commodity_composition</td>
                        <td>No</td>
                        <td>Commodity composition. Max 100 characters</td>
                    </tr>
                    <tr>
                        <td>length</td>
                        <td>No</td>
                        <td>Product length. Between 0 and 9999999.99</td>
                    </tr>
                    <tr>
                        <td>width</td>
                        <td>No</td>
                        <td>Product width. Between 0 and 9999999.99</td>
                    </tr>
                    <tr>
                        <td>height</td>
                        <td>No</td>
                        <td>Product height. Between 0 and 9999999.99</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('script')

<script>
$(function() {

    $(document).ready(function() {

        $('body').on('click', '#toggle-report', function() {
            if($('#report:visible').length == 0){
                $('#toggle-report').html('Click to hide information <i class="fas fa-angle-up"></i>');
                $('#report').slideDown();
            }else{
                $('#toggle-report').html('Click to show more information <i class="fas fa-angle-down"></i>');
                $('#report').slideUp();
            }
        });

        $('body').on('click', '#import-btn', function() {
            $('#mode').val("import");
            $('#csv_form').attr("action", "/import_csv");
            $('#update-btn').removeClass("btn-primary");
            $('#export-btn').removeClass("btn-primary");
            $(this).addClass("btn-primary");
            $('.auto_fill').show();
            $('#file-input').show();
            $('#submit-btn').html("Import CSV");
            $('#submit-btn').prop('disabled', false);
        });
        $('body').on('click', '#update-btn', function() {
            $('#mode').val("update");
            $('#csv_form').attr("action", "/import_csv");
            $('#import-btn').removeClass("btn-primary");
            $('#export-btn').removeClass("btn-primary");
            $(this).addClass("btn-primary");
            $('.auto_fill').show();
            $('#file-input').show();
            $('#submit-btn').html("Update CSV");
            $('#submit-btn').prop('disabled', false);
        });
        $('body').on('click', '#export-btn', function() {
            $('#mode').val("export");
            $('#csv_form').attr("action", "/export_csv");
            $('#update-btn').removeClass("btn-primary");
            $('#import-btn').removeClass("btn-primary");
            $(this).addClass("btn-primary");
            $('.auto_fill').hide();
            $('#file-input').hide();
            $('#submit-btn').html("Export CSV");
            $('#submit-btn').prop('disabled', false);
        });
    });

    $('#csv_form').submit(function(e) {
        e.preventDefault(); // don't submit multiple times
        this.submit(); // use the native submit method of the form element

        $(this).find("button[type='submit']").html('<div class="spinner-border mb-2" role="status"><span class="sr-only">Loading...</span></div>');
        $('#patient').show();
        $(this).find("button[type='submit']").prop('disabled',true);

    });

});
</script>
@endsection
