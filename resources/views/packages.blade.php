@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/packages.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Packages') }}</h4>

            <a data-toggle="modal" data-target="#addPackageModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Package') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="packagesTable">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Days') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Package Modal --}}
    <div class="modal fade" id="addPackageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Package') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="addPackageForm" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Days') }}</label>
                            <input type="number" name="days" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Price') }}</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>                        
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value="{{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Package Modal --}}
    <div class="modal fade" id="editPackageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit Package') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="editPackageForm" autocomplete="off">
                        @csrf
                        <input type="hidden" name="id" id="editPackageId">
                        <div class="form-group">
                            <label>{{ __('Name') }}</label>
                            <input type="text" name="name" id="editPackageName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Days') }}</label>
                            <input type="number" name="days" id="editPackageDays" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Price') }}</label>
                            <input type="number" name="price" id="editPackagePrice" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Description') }}</label>
                            <textarea name="description" id="editPackageDescription" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value="{{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
