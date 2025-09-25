@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/interests.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Interests') }}</h4>

            <a data-toggle="modal" data-target="#addInterestModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Interest') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="interestsTable">
                    <thead>
                        <tr>
                            <th>{{ __('Interest') }}</th>
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

    {{-- Edit Interest Modal --}}
    <div class="modal fade" id="editInterestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit Interest') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="editInterestForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" name="id" id="editInterestId">

                        <div class="form-group">
                            <label> {{ __('Interest') }}</label>
                            <input id="editInterest" type="text" name="name" class="form-control" required>
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
    {{-- Add Interest Modal --}}
    <div class="modal fade" id="addInterestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Interest') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="addInterestForm" autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('Interest') }}</label>
                            <input type="text" name="interest" class="form-control" required>
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
