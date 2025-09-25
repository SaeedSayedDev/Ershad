@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/jobTitles.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('jobTitles') }}</h4>

            <a data-toggle="modal" data-target="#addjobTitleModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add jobTitle') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="jobTitlesTable">
                    <thead>
                        <tr>
                            <th>{{ __('jobTitle') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Edit jobTitle Modal --}}
    <div class="modal fade" id="editjobTitleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit jobTitle') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="editjobTitleForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" name="id" id="editjobTitleId">

                        <div class="form-group">
                            <label> {{ __('jobTitle') }}</label>
                            <input id="editjobTitle" type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Add jobTitle Modal --}}
    <div class="modal fade" id="addjobTitleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add jobTitle') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="addjobTitleForm" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('jobTitle') }}</label>
                            <input type="text" name="name" class="form-control" required>
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
