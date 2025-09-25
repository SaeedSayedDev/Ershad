<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//******************/ Users
Route::prefix('user')->group(function () {
    // Common
    Route::post('user/register/google', [UsersController::class, 'registerUserGoogle']);

    Route::post('registerUser', [UsersController::class, 'registerUser'])->middleware('checkHeader');
    Route::post('updateUserDetails', [UsersController::class, 'updateUserDetails'])->middleware('checkHeader');
    Route::post('deleteUserAccount', [UsersController::class, 'deleteUserAccount'])->middleware('checkHeader');
    Route::post('fetchMyUserDetails', [UsersController::class, 'fetchMyUserDetails'])->middleware('checkHeader');
    Route::post('addPatient', [UsersController::class, 'addPatient'])->middleware('checkHeader');
    Route::post('editPatient', [UsersController::class, 'editPatient'])->middleware('checkHeader');
    Route::post('deletePatient', [UsersController::class, 'deletePatient'])->middleware('checkHeader');
    Route::post('fetchPatients', [UsersController::class, 'fetchPatients'])->middleware('checkHeader');
    Route::post('fetchFavoriteDoctors', [UsersController::class, 'fetchFavoriteDoctors'])->middleware('checkHeader');
    Route::post('fetchHomePageData', [UsersController::class, 'fetchHomePageData'])->middleware('checkHeader');
    Route::post('searchDoctor', [DoctorController::class, 'searchDoctor'])->middleware('checkHeader');
    Route::post('fetchDoctorProfile', [DoctorController::class, 'fetchDoctorProfile'])->middleware('checkHeader');
    Route::post('fetchDoctorReviews', [DoctorController::class, 'fetchDoctorReviews'])->middleware('checkHeader');
    Route::post('logOut', [UsersController::class, 'logOut'])->middleware('checkHeader');

    // Wallet
    Route::post('addMoneyToUserWallet', [UsersController::class, 'addMoneyToUserWallet'])->middleware('checkHeader');
    Route::get('SucessaddMoneyToUserWallet', [UsersController::class, 'SucessaddMoneyToUserWallet'])->name('api.user.SucessaddMoneyToUserWallet');
    Route::post('create_url', [UsersController::class, 'create_url'])->middleware('checkHeader');
    Route::post('fetchWalletStatement', [UsersController::class, 'fetchWalletStatement'])->middleware('checkHeader');
    Route::post('submitUserWithdrawRequest', [UsersController::class, 'submitUserWithdrawRequest'])->middleware('checkHeader');
    Route::post('fetchUserWithdrawRequests', [UsersController::class, 'fetchUserWithdrawRequests'])->middleware('checkHeader');

    // Appointments
    Route::post('fetchAcceptedPendingAppointmentsOfDoctorByDate', [AppointmentController::class, 'fetchAcceptedPendingAppointmentsOfDoctorByDate'])->middleware('checkHeader');
    Route::post('fetchCoupons', [AppointmentController::class, 'fetchCoupons'])->middleware('checkHeader');
    Route::post('addAppointment', [AppointmentController::class, 'addAppointment'])->middleware('checkHeader');
    Route::post('addPayment', [AppointmentController::class, 'addPayment'])->middleware('checkHeader');
    Route::post('rescheduleAppointment', [AppointmentController::class, 'rescheduleAppointment'])->middleware('checkHeader');
    Route::post('cancelAppointment', [AppointmentController::class, 'cancelAppointment'])->middleware('checkHeader');
    Route::post('checkPolicy', [AppointmentController::class, 'checkPolicy'])->middleware('checkHeader');
    Route::post('fetchAppointmentDetails', [AppointmentController::class, 'fetchAppointmentDetails'])->middleware('checkHeader');
    Route::post('addRating', [AppointmentController::class, 'addRating'])->middleware('checkHeader');
    Route::post('fetchMyPrescriptions', [AppointmentController::class, 'fetchMyPrescriptions'])->middleware('checkHeader');
    Route::post('fetchMyAppointments', [AppointmentController::class, 'fetchMyAppointments'])->middleware('checkHeader');

    // Notification
    Route::post('fetchNotification', [UsersController::class, 'fetchNotification'])->middleware('checkHeader');
    Route::get('TEST_sendNotificationToUser', [UsersController::class, 'TEST_sendNotificationToUser']);

    // Promo Codes
    Route::post('checkPromoCode', [PromoCodeController::class, 'checkPromoCode'])->middleware('checkHeader');
});


//******************/ Doctor
Route::post('doctorRegistration', [DoctorController::class, 'doctorRegistration'])->middleware('checkHeader');
Route::post('updateDoctorDetails', [DoctorController::class, 'updateDoctorDetails'])->middleware('checkHeader');
Route::post('updateDoctorOnline', [DoctorController::class, 'updateDoctorOnline'])->middleware('checkHeader');

Route::get('fechDoctorOnline', [DoctorController::class, 'fechDoctorOnline'])->middleware('checkHeader');
Route::post('ChickDoctorOnline', [DoctorController::class, 'ChickDoctorOnline'])->middleware('checkHeader');
Route::post('deleteDoctorAccount', [DoctorController::class, 'deleteDoctorAccount'])->middleware('checkHeader');
Route::post('logOutDoctor', [DoctorController::class, 'logOutDoctor'])->middleware('checkHeader');
Route::post('fetchDoctorCategories', [DoctorController::class, 'fetchDoctorCategories'])->middleware('checkHeader');
Route::get('fetchDoctorSubCategories', [DoctorController::class, 'fetchDoctorSubCategories'])->middleware('checkHeader');
Route::post('fetchDoctorReviews', [DoctorController::class, 'fetchDoctorReviews'])->middleware('checkHeader');
Route::post('suggestDoctorCategory', [DoctorController::class, 'suggestDoctorCategory'])->middleware('checkHeader');
Route::post('fetchDoctorNotifications', [DoctorController::class, 'fetchDoctorNotifications'])->middleware('checkHeader');
Route::post('fetchMyDoctorProfile', [DoctorController::class, 'fetchMyDoctorProfile'])->middleware('checkHeader');
Route::post('addEditService', [DoctorController::class, 'addEditService'])->middleware('checkHeader');
Route::post('addEditAwards', [DoctorController::class, 'addEditAwards'])->middleware('checkHeader');
Route::post('addEditExpertise', [DoctorController::class, 'addEditExpertise'])->middleware('checkHeader');
Route::post('addEditExperience', [DoctorController::class, 'addEditExperience'])->middleware('checkHeader');
Route::post('addEditServiceLocations', [DoctorController::class, 'addEditServiceLocations'])->middleware('checkHeader');
Route::post('addAppointmentSlots', [DoctorController::class, 'addAppointmentSlots'])->middleware('checkHeader');
Route::post('manageDrBankAccount', [DoctorController::class, 'manageDrBankAccount'])->middleware('checkHeader');
Route::post('deleteAppointmentSlot', [DoctorController::class, 'deleteAppointmentSlot'])->middleware('checkHeader');
Route::post('addHoliday', [DoctorController::class, 'addHoliday'])->middleware('checkHeader');
Route::post('deleteHoliday', [DoctorController::class, 'deleteHoliday'])->middleware('checkHeader');
Route::post('fetchFaqCats', [DoctorController::class, 'fetchFaqCats'])->middleware('checkHeader');
Route::post('fetchUserDetails', [DoctorController::class, 'fetchUserDetails'])->middleware('checkHeader');
Route::post('checkMobileNumberExists', [DoctorController::class, 'checkMobileNumberExists'])->middleware('checkHeader');
Route::get('availableDoctorsNow', [DoctorController::class, 'availableDoctorsNow'])->middleware('checkHeader');
Route::post('availableDoctorsByDay', [DoctorController::class, 'availableDoctorsByDay'])->middleware('checkHeader');
Route::post('addDoctorAdds', [DoctorController::class, 'addDoctorAdds'])->middleware('checkHeader');
// Appointments
Route::post('fetchAppointmentRequests', [AppointmentController::class, 'fetchAppointmentRequests'])->middleware('checkHeader');
Route::post('fetchAppointmentDetails', [AppointmentController::class, 'fetchAppointmentDetails']);
Route::post('fetchAcceptedAppointsByDate', [AppointmentController::class, 'fetchAcceptedAppointsByDate'])->middleware('checkHeader');
Route::post('acceptAppointment', [AppointmentController::class, 'acceptAppointment'])->middleware('checkHeader');
Route::post('declineAppointment', [AppointmentController::class, 'declineAppointment'])->middleware('checkHeader');
Route::post('addPrescription', [AppointmentController::class, 'addPrescription'])->middleware('checkHeader');
Route::post('editPrescription', [AppointmentController::class, 'editPrescription'])->middleware('checkHeader');
Route::post('completeAppointment', [AppointmentController::class, 'completeAppointment'])->middleware('checkHeader');
Route::post('fetchAppointmentHistory', [AppointmentController::class, 'fetchAppointmentHistory'])->middleware('checkHeader');
Route::get('callpack_payment_success', [AppointmentController::class, 'callpack_payment_success']);
Route::get('callpack_payment_failure', [AppointmentController::class, 'callpack_payment_failure']);

// Articles
Route::post('fetchArticles', [ArticleController::class, 'fetchArticles']);

// Packages
Route::post('fetchPackages', [PackageController::class, 'fetchPackages']);

// Doctor Promotions
Route::post('askDoctorForPromotion', [PromotionController::class, 'askDoctorForPromotion'])->middleware('checkHeader');
Route::post('fetchDoctorPromotion', [PromotionController::class, 'fetchDoctorPromotion'])->middleware('checkHeader');
Route::post('fetchAllDoctorPromotions', [PromotionController::class, 'fetchAllDoctorPromotions'])->middleware('checkHeader');
Route::post('fetchApprovedDoctorPromotions', [PromotionController::class, 'fetchApprovedDoctorPromotions'])->middleware('checkHeader');

// Contacts
Route::post('fetchContacts', [ContactController::class, 'fetchContacts'])->middleware('checkHeader');

// Questions
Route::post('fetchQuestions', [AiController::class, 'fetchQuestions'])->middleware('checkHeader');

// Wallet
Route::post('fetchDoctorWalletStatement', [AppointmentController::class, 'fetchDoctorWalletStatement'])->middleware('checkHeader');
Route::post('fetchDoctorEarningHistory', [AppointmentController::class, 'fetchDoctorEarningHistory'])->middleware('checkHeader');
Route::post('submitDoctorWithdrawRequest', [AppointmentController::class, 'submitDoctorWithdrawRequest'])->middleware('checkHeader');
Route::post('fetchDoctorPayoutHistory', [AppointmentController::class, 'fetchDoctorPayoutHistory'])->middleware('checkHeader');

// Settings
Route::post('fetchGlobalSettings', [SettingsController::class, 'fetchGlobalSettings'])->middleware('checkHeader');

Route::post('uploadFileGivePath', [SettingsController::class, 'uploadFileGivePath'])->middleware('checkHeader');
Route::post('generateAgoraToken', [SettingsController::class, 'generateAgoraToken'])->middleware('checkHeader');
Route::get('fetchAllInterests', [SettingsController::class, 'fetchAllInterests']);
Route::get('fetchAllSliders', [SettingsController::class, 'fetchAllSliders']);
Route::get('fetchAllJobTitles', [SettingsController::class, 'fetchAllJobTitles']);














Route::get('/test/firebase', function (Request $request) {
    // dd('dd');
    $firebase = new FirebaseService();

    $response = $firebase->sendNotification(
        'fjK98AtoQ0yf2MduCPMk8m:APA91bE9Si0B3N3SZbffbo_MOVwVuDvs_b8PTxzjF2113Lj4lgoW0gsC_U_W8-wy4YImh231bVYaQBCd4Rnm3nTW90qLY8_xIkc5z6WmYTl1t2SDTPiul6I',
        'Hello Said  ',
        'This is a test notification using FCM '
    );

    return $response;
});