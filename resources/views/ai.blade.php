@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/ai.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('AI') }}</h4>

            <a data-toggle="modal" data-target="#addQuestionModal" href="" class="ml-auto btn btn-primary text-white">
                {{ __('Add Question') }}
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-pills border-b mb-3  ml-0">
                <li role="presentation" class="nav-item">
                    <a class="nav-link pointer active" href="#Section1" role="tab" data-toggle="tab">
                        {{ __('Questions') }}
                        <span class="badge badge-transparent "></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content tabs" id="home">
                {{-- Section 1 --}}
                <div role="tabpanel" class="row tab-pane active" id="Section1">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="questionsTable">
                            <thead>
                                <tr>
                                    <th class="w-30">{{ __('Question') }}</th>
                                    <th>{{ __('Category') }}</th>
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

    {{-- Add Question Modal --}}
    <div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Question') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="addQuestionForm"
                        autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('Category') }}</label>

                            <div class="form-group">
                                <select name="category_id" id="category" class="form-control form-control-sm"
                                    aria-label="Default select example">
                                    @foreach ($cats as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Question') }}</label>
                                <input type="text" name="question" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 1') }}</label>
                                <input type="text" name="choices[]" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 2') }}</label>
                                <input type="text" name="choices[]" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 3') }}</label>
                                <input type="text" name="choices[]" class="form-control">
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 4') }}</label>
                                <input type="text" name="choices[]" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Question Modal --}}
    <div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Question') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="editQuestionForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" id="editQuestionId" name="id">

                        <div class="form-group">
                            <label> {{ __('Category') }}</label>
                            <div class="form-group">
                                <select name="category_id" id="editQuestionCategory" class="form-control form-control-sm"
                                    aria-label="Default select example">

                                </select>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Question') }}</label>
                                <input id="editQuestion" type="text" name="question" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 1') }}</label>
                                <input id="editChoice1" type="text" name="choices[]" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 2') }}</label>
                                <input id="editChoice2" type="text" name="choices[]" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 3') }}</label>
                                <input id="editChoice3" type="text" name="choices[]" class="form-control">
                            </div>

                            <div class="form-group">
                                <label> {{ __('Choice 4') }}</label>
                                <input id="editChoice4" type="text" name="choices[]" class="form-control">
                            </div>
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
