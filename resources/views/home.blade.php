@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    URL: {{ config('app.url') }}

                    AssetName: {{ config('app.asset_name') }}

                    <form method="POST" action="{{ route('store') }}" enctype='multipart/form-data'>
                        {{ csrf_field() }}

                        <input type="file" name="file" accept="mp4">
                        <button type="submit">submit</button>
                    </form>

                    <a href="{{ route('show') }}">test job</a>

                    <form method="POST" action="{{ route('download') }}" enctype='multipart/form-data'>
                        {{ csrf_field() }}

                        <button type="submit">download</button>
                    </form>

                </div>

                <chunk-uploader></chunk-uploader>
            </div>
        </div>
    </div>
</div>
@endsection
