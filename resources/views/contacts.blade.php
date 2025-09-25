@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/contacts.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Contacts') }}</h4>
            <a data-toggle="modal" data-target="#addContactModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Contact') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="contactsTable">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Contact Modal --}}
    <div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Contact') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="addContactForm"
                        autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Type') }}</label>
                            <input type="text" name="type" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Value') }}</label>
                            <input type="text" name="value" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Link') }}</label>
                            <input type="text" name="link" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value="{{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Contact Modal --}}
    <div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit Contact') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="editContactForm"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="id" id="editContactId">
                        <div class="form-group">
                            <label>{{ __('Type') }}</label>
                            <input type="text" name="type" id="editContactType" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Value') }}</label>
                            <input type="text" name="value" id="editContactValue" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Link') }}</label>
                            <input type="text" name="link" id="editContactLink" class="form-control" required>
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
