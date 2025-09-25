@extends('include.app')

@section('header')
    <script src="{{ asset('asset/script/promotions.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Doctor Promotions') }}</h4>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills border-b mb-3 ml-0">
                <li role="presentation" class="nav-item"><a class="nav-link pointer active" href="#allSection"
                        aria-controls="home" role="tab" data-toggle="tab">{{ __('All Promotions') }}<span
                            class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#pendingSection"
                        role="tab" data-toggle="tab">{{ __('Pending') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#approvedSection"
                        role="tab" data-toggle="tab">{{ __('Approved') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#rejectedSection"
                        role="tab" data-toggle="tab">{{ __('Rejected') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
            </ul>

            <div class="tab-content tabs" id="home">
                {{-- All --}}
                <div role="tabpanel" class="row tab-pane active" id="allSection">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="allPromotionsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Doctor') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pending --}}
                <div role="tabpanel" class="row tab-pane" id="pendingSection">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="pendingPromotionTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Doctor') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Approved --}}
                <div role="tabpanel" class="row tab-pane" id="approvedSection">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="approvedPromotionTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Doctor') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Expiration Date') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Rejected --}}
                <div role="tabpanel" class="row tab-pane" id="rejectedSection">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100" id="rejectedPromotionTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Doctor') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Rejected Reason') }}</th>
                                    <th>{{ __('Created At') }}</th>
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

    {{-- Add Rejection Reason Modal --}}
    <div class="modal fade" id="addRejectionReasonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Rejection Reason') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="addRejectionReasonForm"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="id" id="promotionId">
                        <div class="form-group">
                            <label> {{ __('Rejection Reason') }}</label>
                            <textarea name="rejection_reason" class="form-control"></textarea>
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
