@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/sliders.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('sliders') }}</h4>

            <a data-toggle="modal" data-target="#addsliderModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add slider') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="slidersTable">
                    <thead>
                        <tr>
                            <th>{{ __('slider') }}</th>
                            <th>{{ __('link') }}</th>
                            <th>{{ __('image') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Edit slider Modal --}}
    <div class="modal fade" id="editsliderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit slider') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="editsliderForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" name="id" id="editsliderId">

                        <div class="form-group">
                            <label> {{ __('tilte') }}</label>
                            <input id="editslider" type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('link') }}</label>
                            <input id="editlink" type="text" name="link" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Image') }}</label>
                            <input id="editImage" type="file" name="file" class="form-control" >
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Add slider Modal --}}
    <div class="modal fade" id="addsliderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add slider') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="addsliderForm" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('title') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('link') }}</label>
                            <input type="text" name="link" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Image') }}</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
