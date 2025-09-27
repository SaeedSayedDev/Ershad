@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/doctors.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Doctors') }}</h4>
            <a data-toggle="modal" data-target="#addDoctorsModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Doctor') }}</a>
        </div>

        <div class="card-body">
            <ul class="nav nav-pills border-b mb-3  ml-0">

                <li role="presentation" class="nav-item"><a class="nav-link pointer active" href="#Section1"
                        aria-controls="home" role="tab" data-toggle="tab">{{ __('All Doctors') }}<span
                            class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section2" aria-controls="home"
                        role="tab" data-toggle="tab">{{ __('Approved') }}<span
                            class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section3" aria-controls="home"
                        role="tab" data-toggle="tab">{{ __('Pending') }}<span
                            class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#Section4" aria-controls="home"
                        role="tab" data-toggle="tab">{{ __('Banned') }}<span
                            class="badge badge-transparent "></span></a>
                </li>

            </ul>

            <div class="tab-content tabs" id="home">
                {{-- Section 1 --}}
                <div role="tabpanel" class="row tab-pane active" id="Section1">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="allDoctorsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Number') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Experience') }}</th>
                                    <th>{{ __('Total Patients Cured') }}</th>
                                    <th>{{ __('Lifetime Earnings') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Section 2 --}}
                <div role="tabpanel" class="row tab-pane" id="Section2">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="approvedDoctorsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Number') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Experience') }}</th>
                                    <th>{{ __('Total Patients Cured') }}</th>
                                    <th>{{ __('Lifetime Earnings') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Section 3 --}}
                <div role="tabpanel" class="row tab-pane" id="Section3">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="pendingDoctorsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Number') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Experience') }}</th>
                                    <th>{{ __('Total Patients Cured') }}</th>
                                    <th>{{ __('Lifetime Earnings') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Section 4 --}}
                <div role="tabpanel" class="row tab-pane" id="Section4">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="bannedDoctorsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Number') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Experience') }}</th>
                                    <th>{{ __('Total Patients Cured') }}</th>
                                    <th>{{ __('Lifetime Earnings') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="modal fade" id="addDoctorsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Doctor') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="addDoctorsForm"
                        autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('Doctor') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Email') }}</label>
                            <input type="email" name="identity" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Category') }}</label>
                            <select name="category_id[]" class="form-control" multiple required>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">اضغط Ctrl أو ⌘ لاختيار أكثر من تصنيف</small>
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
