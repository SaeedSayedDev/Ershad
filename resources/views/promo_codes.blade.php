<!-- resources/views/promo_codes.blade.php -->

@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/promo_codes.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Promo Codes') }}</h4>

            <a data-toggle="modal" data-target="#addPromoCodeModal" href=""
                class="ml-auto btn btn-primary text-white">{{ __('Add Promo Code') }}</a>
        </div>
        <div class="card-body">
            <div class="table-responsive col-12">
                <table class="table table-striped w-100 word-wrap" id="promoCodesTable">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Percentage') }}</th>
                            <th>{{ __('Max. Discount Amount') }}</th>
                            <th>{{ __('Heading') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('User ID') }}</th>
                            <th>{{ __('Expiration Date') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Add Promo Code Modal --}}
    <div class="modal fade" id="addPromoCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Promo Code') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="addPromoCodeForm"
                        autocomplete="off">
                        @csrf

                        <div class="form-group">
                            <label> {{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Heading') }}</label>
                            <input type="text" name="heading" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> {{ __('Description') }}</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label> {{ __('Percentage') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            %
                                        </div>
                                    </div>
                                    <input name="percentage" type="number" class="form-control currency" min="0"
                                        required>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label>{{ __('Max. Discount Amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{ $settings->currency }}
                                        </div>
                                    </div>
                                    <input name="max_discount_amount" type="number" class="form-control currency"
                                        min="0" required>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>{{ __('Expiration Date') }}</label>
                            <input type="date" name="expired_at" class="form-control" min="{{ date('Y-m-d') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Edit Promo Code Modal --}}
    <div class="modal fade" id="editPromoCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Edit Promo Code') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="editPromoCodeForm"
                        autocomplete="off">
                        @csrf

                        <input type="hidden" name="id" id="editPromoCodeId">

                        <div class="form-group">
                            <label> {{ __('Code') }}</label>
                            <input id="editCode" type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label> {{ __('Heading') }}</label>
                            <input id="editHeading" type="text" name="heading" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> {{ __('Description') }}</label>
                            <input id="editDescription" type="text" name="description" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label> {{ __('Percentage') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            %
                                        </div>
                                    </div>
                                    <input id="editPercentage" name="percentage" type="number"
                                        class="form-control currency" min="0" required>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label>{{ __('Max. Discount Amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{ $settings->currency }}
                                        </div>
                                    </div>
                                    <input id="editMaxDiscAmount" name="max_discount_amount" type="number"
                                        class="form-control currency" min="0" required>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>{{ __('Expiration Date') }}</label>
                            <input type="date" name="expired_at" id="editExpiredAt" class="form-control"
                                min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // إخفاء عمود User ID بعد تحميل الجدول
            $('#promoCodesTable').on('init.dt', function() {
                $('#promoCodesTable').DataTable().column(5).visible(false);
            });
        });
    </script>
@endsection
