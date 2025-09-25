@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/articles.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Articles') }}</h4>

            <a data-toggle="modal" data-target="#addArticleModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Article') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="articlesTable">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Published Date') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Article Modal --}}
    <div class="modal fade" id="addArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Article') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="addArticleForm"
                        autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label> {{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Image') }}</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Content') }}</label>
                            <textarea name="content" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Article Modal --}}
    <div class="modal fade" id="editArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit Article') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="editArticleForm"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="id" id="editArticleId">
                        <div class="form-group">
                            <label> {{ __('Title') }}</label>
                            <input type="text" name="title" id="editArticleTitle" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Image') }}</label>
                            <input type="file" name="image" id="editArticleImage" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> {{ __('Content') }}</label>
                            <textarea name="content" id="editArticleContent" class="form-control" required></textarea>
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
