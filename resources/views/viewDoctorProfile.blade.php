@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/viewDoctorProfile.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('asset/style/viewDoctorProfile.css') }}">
@endsection

@section('content')
    <style>
        .text-grey {
            color: grey !important;
        }

        .p-data {
            font-size: 20px !important;
            font-weight: bold !important;
        }

        .img-border {
            border: 2px solid #313131 !important;
        }

        .bg-cat-tag {
            background: #d8f6fd;
            color: #156779 !important;
        }

        .word-wrap td {
            white-space: normal !important;
        }

        .word-wrap tr th:nth-child(3),
        .word-wrap tr td:nth-child(3) {
            width: 200px !important;
            white-space: normal !important;
        }

        .salon_image {
            position: relative;
            margin-right: 15px;
            cursor: pointer;
        }

        .salon_image i {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #ffffff4f;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 14px;
            color: #fff;
            backdrop-filter: blur(7px);
        }

        #tabGallery table.dataTable td {
            white-space: normal !important;
        }

        .w-70 {
            width: 70% !important;
        }

        .w-30 {
            width: 30% !important;
        }

        .starDisabled {
            color: rgb(184, 184, 184) !important;
        }

        .starActive {
            color: orangered !important;
        }

        .ic-debit {
            font-size: 17px !important;
            color: rgb(255, 63, 63);
        }

        .ic-credit {
            font-size: 17px !important;
            color: rgb(69, 136, 72);
        }

        .text-debit {
            color: rgb(255, 63, 63);
        }

        .text-credit {
            color: rgb(69, 136, 72);
        }

        .bank-details span {
            display: block;
            line-height: 18px;
        }

        .btn-close-slot {
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 14px;
            line-height: 1;
            padding: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-close-slot:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-50%) scale(1.1);
        }

        .badge.position-relative {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
    </style>
    <input type="hidden" value="{{ $doctor->id }}" id="doctorId">

    <div class="card">
        <div class="card-header">
            <img class="rounded-circle img-border mr-2" width="40" height="40"
                src="{{ env('FILES_BASE_URL') }}{{ $doctor->image }}" alt="">
            <h4>
                {{ $doctor->name }} / {{ $doctor->doctor_number }}
            </h4>


            {{-- Doctor Status --}}
            @if ($doctor->status == $doctorStatus['statusDoctorPending'])
                <span class="badge bg-warning text-white ">{{ __('Pending Review') }} </span>
            @elseif($doctor->status == $doctorStatus['statusDoctorApproved'])
                <span class="badge bg-success text-white ">{{ __('Active') }} </span>
            @elseif($doctor->status == $doctorStatus['statusDoctorBanned'])
                <span class="badge bg-danger text-white ">{{ __('Banned') }} </span>
            @endif


            {{-- Action Buttons --}}
            @if ($doctor->status == $doctorStatus['statusDoctorPending'])
                <a href="" id="approveDoctor"
                    class="ml-auto btn btn-success activateDoctor">{{ __('Approve Doctor') }}</a>
            @elseif($doctor->status == $doctorStatus['statusDoctorApproved'])
                <a href="" id="banDoctor" class="ml-auto btn btn-danger text-white">{{ __('Ban Doctor') }}</a>
            @elseif($doctor->status == $doctorStatus['statusDoctorBanned'])
                <a href="" id="activateDoctor"
                    class="ml-auto btn btn-success activateDoctor">{{ __('Activate Doctor') }}</a>
            @endif

        </div>
        <div class="card-body">

            <div class="form-row">

                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Mobile Number') }}</label>
                    <p class="mt-0 p-data">{{ $doctor->mobile_number }}</p>
                </div>
                <div class="col-md-2">
                    <label class="mb-0 text-grey d-block" for="">{{ __('Gender') }}</label>
                    @if ($doctor->gender == 1)
                        <span class="badge bg-primary text-white ">{{ __('Male') }}</span>
                    @else
                        <span class="badge bg-info text-white ">{{ __('Female') }}</span>
                    @endif
                </div>

                <div class="col-md-2">
                    <label class="mb-0 text-grey d-block">{{ __('Category') }}</label>

                    @if (!empty($doctor->categories) && count($doctor->categories) > 0)
                        @foreach ($doctor->categories as $cat)
                            <span class="badge bg-primary text-white m-1">{{ $cat->title }}</span>
                        @endforeach
                    @else
                        <span class="badge bg-secondary text-white">{{ __('No Category') }}</span>
                    @endif
                </div>


                {{-- Online consultation --}}
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Online Consultation') }}</label>
                    @if ($doctor->online_consultation == 0)
                        <p class="mt-0 p-data">{{ __('No') }}</p>
                    @else
                        <p class="mt-0 p-data">{{ __('Yes') }}</p>
                    @endif
                </div>
                {{-- Clinic consultation --}}
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Clinic Consultation') }}</label>
                    @if ($doctor->clinic_consultation == 0)
                        <p class="mt-0 p-data">{{ __('No') }}</p>
                    @else
                        <p class="mt-0 p-data">{{ __('Yes') }}</p>
                    @endif
                </div>

            </div>

            <div class="form-row mt-3">
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Wallet') }}</label>
                    <p class="mt-0 p-data">{{ $settings->currency }}{{ $doctor->wallet }}</p>
                </div>
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Lifetime Earnings') }}</label>
                    <p class="mt-0 p-data">{{ $settings->currency }}{{ $doctor->lifetime_earnings }}</p>
                </div>
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Total Patients Cured') }}</label>
                    <p class="mt-0 p-data">{{ $doctor->total_patients_cured }}</p>
                </div>

                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('Overall Rating') }}</label>
                    <p class="mt-0 p-data">{{ $doctor->rating }}</p>
                </div>

                {{-- On Vacation Status --}}
                <div class="col-md-2">
                    <label class="mb-0 text-grey" for="">{{ __('On Vacation') }}</label>
                    @if ($doctor->on_vacation == 0)
                        <p class="mt-0 p-data">{{ __('No') }}</p>
                    @else
                        <p class="mt-0 p-data">{{ __('Yes') }}</p>
                    @endif
                </div>
            </div>
            <div class="form-row mt-2">
                <div class="col-md-2">
                    <div class="bank-details">
                        <span>{{ __('Bank Details :') }}</span>
                        @if ($doctor->bankAccount != null)
                            <span class="text-dark font-14">{{ __('Holder : ') }}{{ $doctor->bankAccount->holder }}</span>
                            <span
                                class="text-dark font-14">{{ __('Bank : ') }}{{ $doctor->bankAccount->bank_name }}</span>
                            <span
                                class="text-dark font-14">{{ __('Account : ') }}{{ $doctor->bankAccount->account_number }}</span>
                            <span
                                class="text-dark font-14">{{ __('Swift Code : ') }}{{ $doctor->bankAccount->swift_code }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="bank-details">
                        <span>{{ __('Clinic Details :') }}</span>

                        <span class="text-dark font-14">{{ __('Clinic Name : ') }}{{ $doctor->clinic_name }}</span>
                        <span class="text-dark font-14">{{ __('Clinic Address : ') }}{{ $doctor->clinic_address }}</span>

                        <a target="_blank" class="badge bg-primary text-white mt-1"
                            href="https://www.google.com/maps/?q={{ $doctor->clinic_lat }},{{ $doctor->clinic_long }}">{{ __('Click To Locate') }}</a>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills border-b  ml-0">

                <li role="presentation" class="nav-item"><a class="nav-link pointer active" href="#tabDetails"
                        aria-controls="home" role="tab" data-toggle="tab">{{ __('Details') }}<span
                            class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabAppointments" role="tab"
                        aria-controls="tabAppointments" data-toggle="tab">{{ __('Appointments') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabReviews" role="tab"
                        aria-controls="tabReviews" data-toggle="tab">{{ __('Reviews') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabWallet" role="tab"
                        data-toggle="tab">{{ __('Wallet') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabPayOuts" role="tab"
                        data-toggle="tab">{{ __('Payouts') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabEarningHistory"
                        role="tab" data-toggle="tab">{{ __('Earnings') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabServices" role="tab"
                        data-toggle="tab">{{ __('Services') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabExpertise"
                        role="tab" data-toggle="tab">{{ __('Expertise') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabExperience"
                        role="tab" data-toggle="tab">{{ __('Experience') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabServiceLocations"
                        role="tab" data-toggle="tab">{{ __('Service Locations') }}
                        <span class="badge badge-transparent "></span></a>
                </li>

                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabAwards" role="tab"
                        data-toggle="tab">{{ __('Awards') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabHolidays" role="tab"
                        data-toggle="tab">{{ __('Holidays') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#tabSlots" role="tabSlots"
                        data-toggle="tab">{{ __('Slots') }}
                        <span class="badge badge-transparent "></span></a>
                </li>
                <li role="presentation" class="nav-item"><a class="nav-link pointer" href="#AddSlot" role="AddSlot"
                        data-toggle="tab">{{ __('Add Slots') }}
                        <span class="badge badge-transparent "></span></a>
                </li>



            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content tabs" id="home">

                {{-- Details --}}
                <div role="tabpanel" class="tab-pane active" id="tabDetails">

                    <div class="form-group mt-0">
                        <div class="d-flex mb-2">
                            <div class="salon_image">
                                <img width="100" class="rounded" height="100"
                                    src="{{ env('FILES_BASE_URL') }}{{ $doctor->image }}" alt="">
                            </div>
                        </div>
                    </div>


                    <form action="" method="post" enctype="multipart/form-data" class=""
                        id="doctorDetailsForm" autocomplete="off">
                        @csrf

                        <input type="hidden" name="id" value="{{ $doctor->id }}">
                        <div class="form-row ">
                            <div class="form-group col-md-3">
                                <label for="">{{ __('image') }}</label>
                                <input type="file" class="form-control" name="file">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ $doctor->name }}">
                            </div>
                        </div>
                        <div class="form-row ">
                            <div class="form-group col-md-3">
                                <label for="">{{ __('Designation') }}</label>
                                <input type="text" class="form-control" name="designation"
                                    value="{{ $doctor->designation }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('Languages Spoken') }}</label>
                                <input type="text" class="form-control" name="languages_spoken"
                                    value="{{ $doctor->languages_spoken }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('Experience Years') }}</label>
                                <input type="number" class="form-control" name="experience_year"
                                    value="{{ $doctor->experience_year }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('Consultation Fees') }}</label>
                                <input type="number" class="form-control" name="consultation_fee"
                                    value="{{ $doctor->consultation_fee }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">{{ __('Total Patients Cured') }}</label>
                                <input type="number" class="form-control" name="total_patients_cured"
                                    value="{{ $doctor->total_patients_cured }}">
                            </div>
                        </div>

                        <div class="form-row ">
                            <div class="form-group col-md-4">
                                <label for="">{{ __('Degrees') }}</label>


                                <textarea style="height:100px !important;" type="text" class="form-control" name="degrees">{{ $doctor->degrees }}</textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">{{ __('About') }}</label>
                                <textarea style="height:100px !important;" type="text" class="form-control" name="about_youself">{{ $doctor->about_youself }}</textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">{{ __('Educational Journey') }}</label>
                                <textarea style="height:100px !important;" type="text" class="form-control" name="educational_journey">{{ $doctor->educational_journey }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>


                </div>
                {{-- Appointments --}}
                <div role="tabpanel" class="tab-pane" id="tabAppointments">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="appointmentsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Appointments Number') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date & Time') }}</th>
                                    <th>{{ __('Service Amount') }}</th>
                                    <th>{{ __('Discount Amount') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th>{{ __('Total Tax Amount') }}</th>
                                    <th>{{ __('Payable Amount') }}</th>
                                    <th>{{ __('Order Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Reviews --}}
                <div role="tabpanel" class="tab-pane" id="tabReviews">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="reviewsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Rating') }}</th>
                                    <th class="w-30">{{ __('Comment') }}</th>
                                    <th>{{ __('Appointment') }}</th>
                                    <th>{{ __('Date&Time') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Wallet --}}
                <div role="tabpanel" class="tab-pane" id="tabWallet">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="walletStatementTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Transaction ID') }}</th>
                                    <th>{{ __('Summary') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Credit/Debit') }}</th>
                                    <th>{{ __('Date & Time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Payouts --}}
                <div role="tabpanel" class="tab-pane" id="tabPayOuts">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="doctorPayOutsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Request Number') }}</th>
                                    <th>{{ __('Bank Details') }}</th>
                                    <th>{{ __('Amount & Status') }}</th>
                                    <th>{{ __('Placed On') }}</th>
                                    <th>{{ __('Summary') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Earnings List --}}
                <div role="tabpanel" class="tab-pane" id="tabEarningHistory">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="earningsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Earning Number') }}</th>
                                    <th>{{ __('Appointment Number') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Services --}}
                <div role="tabpanel" class="tab-pane" id="tabServices">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="servicesTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Experitise --}}
                <div role="tabpanel" class="tab-pane" id="tabExpertise">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="expertiseTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Service Locations --}}
                <div role="tabpanel" class="tab-pane" id="tabServiceLocations">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="serviceLocationsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Hospital Name') }}</th>
                                    <th>{{ __('Hospital Address') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Experience --}}
                <div role="tabpanel" class="tab-pane" id="tabExperience">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="experienceTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Experience --}}
                <div role="tabpanel" class="tab-pane" id="tabAwards">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="awardsTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Experience --}}
                <div role="tabpanel" class="tab-pane" id="tabHolidays">
                    <div class="table-responsive col-12">
                        <table class="table table-striped w-100 word-wrap" id="holidaysTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Slots --}}
                <div role="tabpanel" class="tab-pane" id="tabSlots">
                    <div class="table-responsive col-12">

                        <!-- Monday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Monday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['mondaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}">
                                            @csrf
                                            @method('DELETE') <!-- أضف هذا السطر -->
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tuesday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Tuesday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['tuesdaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Wednesday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Wednesday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['wednesdaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Thursday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Thursday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['thursdaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Friday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Friday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['fridaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Saturday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Saturday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['saturdaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                                            @csrf
                                            @method('DELETE') <!-- أضف هذا السطر -->
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sunday -->
                        <div class="mt-2">
                            <label class="mb-0 text-grey" for="">{{ __('Sunday') }}</label>
                            <div class="d-inline-block">
                                @foreach ($slots['sundaySlots'] as $item)
                                    <span class="badge bg-info text-white position-relative mr-1 mb-1"
                                        style="padding-right: 28px;">
                                        {{ $item['time'] }}
                                        <form method="POST" action="{{ route('admin.delete.slot') }}"
                                            style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                            <button type="submit" class="btn-close-slot"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')"
                                                title="{{ __('Delete') }}">×</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="AddSlot">

                    <div class="table-responsive col-12">
                        <form action="" method="post" enctype="multipart/form-data" class=""
                            id="addSlotForm" autocomplete="off">
                            @csrf

                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

                            <div class="form-row ">
                                <div class="form-group col-md-3">
                                    <label for="">{{ __('time') }}</label>
                                    <input type="time" class="form-control" name="time">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">{{ __('weekday') }}</label>
                                    <select class="form-control" name='weekday'>
                                        <option value='1'>Monday</option>
                                        <option value='2'>Tuesday</option>
                                        <option value='3'>Wednesday</option>
                                        <option value='4'>Thursday</option>
                                        <option value='5'>Friday</option>
                                        <option value='6'>Saturday</option>
                                        <option value='7'>Sunday</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">{{ __('booking limit') }}</label>
                                    <input type="number" class="form-control" name="booking_limit">
                                </div>


                                <div class="form-group col-md-3">
                                    <label for=""></label>
                                    <input class="btn btn-primary mr-1"style="display: block; margin-top: 12px;"
                                        type="submit" value=" {{ __('Submit') }}">
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Preview Gallery Modal --}}
    <div class="modal fade" id="previewGalleryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Preview Gallery Post') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="descGalleryPreview"></p>
                    <img class="rounded" width="100%" id="imggalleryPreview" src="" alt="">
                </div>

            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Reject Withdrawal') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="rejectForm"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" id="rejectId" name="id">
                        <div class="form-group">
                            <label> {{ __('Summary') }}</label>
                            <textarea rows="10" style="height:200px !important;" type="text" name="summary" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary mr-1" type="submit" value=" {{ __('Submit') }}">
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- Complete Modal --}}
    <div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Complete Withdrawal') }}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="" method="post" enctype="multipart/form-data" id="completeForm"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" id="completeId" name="id">
                        <div class="form-group">
                            <label> {{ __('Summary') }}</label>
                            <textarea rows="10" style="height:200px !important;" type="text" name="summary" class="form-control"></textarea>
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
