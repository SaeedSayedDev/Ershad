<!DOCTYPE html>
<html lang="en">


<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ __('App Name') }}</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="asset/css/app.min.css">
    <link rel="stylesheet" href="asset/bundles/bootstrap-social/bootstrap-social.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="stylesheet" href="asset/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="asset/css/custom.css">
    <link rel="stylesheet" href="asset/css/loginPage.css">
    <link rel='shortcut icon' type='image/x-icon' href='asset/img/favicon.png' />
</head>

<body>
    <div class="loader"></div>

 
        <div >
            <div class="main-login-one-box  center">
                <div class="center container ">
                    <div class="form-login-main-box ">
                        



                        <h1 class="login-headr font-30 gil-heavy m-0">Enter your email and password</h1>
                        <h2 class="login-title font-20 gil-reg  mb-4">To delete your account</h2>

                        <form method="POST" action="account_delete">
                             <div class="form-x-box main-card ">
                                 <!-- alert-box -->
                  
                        @if (Session::has('message'))
                            <div class="center-h alert-err mb-4 ">
                                <div class="d-flex ">
                                    <div class="px-2 m-0 center ">
                                        <iconify-icon icon="ep:warning-filled" class="font-alert"></iconify-icon>
                                    </div>
                                    <div class="center">
                                        <span
                                            class="m-0 alert-title-doctor gil-reg font-16">{{ Session::get('message') }}
                                            @php Session::pull('message')
                                            @endphp
                                            </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                            @csrf
                           
                                <div>
                                    <div class="d-flex flex-column mb-3 w-100">
                                        <label for="Username" class="gil-med font-18 text-salon-black">Username</label>
                                        <input name="user_name" type="text" class="login-fild gil-med font-18 px-3"
                                            required id="user_name">
                                    </div>
                                    <div class="d-flex flex-column mb-3 w-100">
                                        <label for="password" class="gil-med font-18 text-salon-black">Password</label>
                                        <input name="user_password" type="password"
                                            class="login-fild gil-med font-18 px-3" required id="user_password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark">
                                    <p class=" gil-med text-white m-0">delete</p>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- General JS Scripts -->
    <script src="asset/js/app.min.js"></script>
    <!-- JS Libraies -->
    <!-- Page Specific JS File -->
    <!-- Template JS File -->
    <script src="asset/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="asset/js/custom.js"></script>
</body>


<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->

</html>
