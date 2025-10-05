<?php

use App\Http\Controllers\AddsController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LoyaltyPointsController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::get('/', [LoginController::class, 'login'])->name('/');
Route::get('/delete_account', [LoginController::class, 'delete_account'])->name('/delete_account');
Route::post('/account_delete', [LoginController::class, 'account_delete'])->name('/account_delete');
Route::post('login', [LoginController::class, 'checklogin'])->middleware(['checkLogin'])->name('login');
Route::get('index', [SettingsController::class, 'index'])->middleware(['checkLogin'])->name('index');
Route::get('logout', [LoginController::class, 'logout'])->middleware(['checkLogin'])->name('logout');

// Users
Route::get('users', [UsersController::class, 'users'])->middleware(['checkLogin'])->name('users');
Route::post('fetchUsersList', [UsersController::class, 'fetchUsersList'])->middleware(['checkLogin'])->name('fetchUsersList');
Route::get('blockUserFromAdmin/{id}', [UsersController::class, 'blockUserFromAdmin'])->middleware(['checkLogin'])->name('blockUserFromAdmin');
Route::get('unblockUserFromAdmin/{id}', [UsersController::class, 'unblockUserFromAdmin'])->middleware(['checkLogin'])->name('unblockUserFromAdmin');

// Doctors
Route::get('doctors', [DoctorController::class, 'doctors'])->middleware(['checkLogin'])->name('doctors');
Route::post('fetchAllDoctorsList', [DoctorController::class, 'fetchAllDoctorsList'])->middleware(['checkLogin'])->name('fetchAllDoctorsList');
Route::post('fetchApprovedDoctorsList', [DoctorController::class, 'fetchApprovedDoctorsList'])->middleware(['checkLogin'])->name('fetchApprovedDoctorsList');
Route::post('fetchPendingDoctorsList', [DoctorController::class, 'fetchPendingDoctorsList'])->middleware(['checkLogin'])->name('fetchPendingDoctorsList');
Route::post('fetchBannedDoctorsList', [DoctorController::class, 'fetchBannedDoctorsList'])->middleware(['checkLogin'])->name('fetchBannedDoctorsList');
Route::get('getDoctorCats', [DoctorController::class, 'getDoctorCats'])->middleware(['checkLogin'])->name('getDoctorCats');
Route::post('addAppointmentSlotsWeb', [DoctorController::class, 'addAppointmentSlotsWeb'])->middleware('checkLogin');

// Appointments
Route::get('appointments', [AppointmentController::class, 'appointments'])->middleware(['checkLogin'])->name('appointments');
Route::post('fetchAllAppointmentsList', [AppointmentController::class, 'fetchAllAppointmentsList'])->middleware(['checkLogin'])->name('fetchAllAppointmentsList');
Route::post('fetchPendingAppointmentsList', [AppointmentController::class, 'fetchPendingAppointmentsList'])->middleware(['checkLogin'])->name('fetchPendingAppointmentsList');
Route::post('fetchAcceptedAppointmentsList', [AppointmentController::class, 'fetchAcceptedAppointmentsList'])->middleware(['checkLogin'])->name('fetchAcceptedAppointmentsList');
Route::post('fetchCompletedAppointmentsList', [AppointmentController::class, 'fetchCompletedAppointmentsList'])->middleware(['checkLogin'])->name('fetchCompletedAppointmentsList');
Route::post('fetchCancelledAppointmentsList', [AppointmentController::class, 'fetchCancelledAppointmentsList'])->middleware(['checkLogin'])->name('fetchCancelledAppointmentsList');
Route::post('fetchDeclinedAppointmentsList', [AppointmentController::class, 'fetchDeclinedAppointmentsList'])->middleware(['checkLogin'])->name('fetchDeclinedAppointmentsList');
Route::post('AddAppointmentsForAdmin', [AppointmentController::class, 'AddAppointmentsForAdmin'])->middleware(['checkLogin'])->name('AddAppointmentsForAdmin');
Route::post('updateAppointmentForAdmin/{id}', [AppointmentController::class, 'updateAppointmentForAdmin'])->middleware(['checkLogin'])->name('updateAppointmentForAdmin');
Route::get('getAppointmentForEdit/{id}', [AppointmentController::class, 'getAppointmentForEdit'])->middleware(['checkLogin'])->name('getAppointmentForEdit');
Route::delete('deleteAppointmentForAdmin/{id}', [AppointmentController::class, 'deleteAppointmentForAdmin'])->name('deleteAppointmentForAdmin');

// Doctor Promotions
Route::get('promotions', [PromotionController::class, 'promotions'])->middleware(['checkLogin'])->name('promotions');
Route::get('approvePromotion/{id}', [PromotionController::class, 'approvePromotion'])->middleware(['checkLogin'])->name('approvePromotion');
Route::post('rejectPromotion', [PromotionController::class, 'rejectPromotion'])->middleware(['checkLogin'])->name('rejectPromotion');
Route::post('fetchAllPromotionsList', [PromotionController::class, 'fetchAllPromotionsList'])->middleware(['checkLogin'])->name('fetchAllPromotionsList');
Route::post('fetchPendingPromotionsList', [PromotionController::class, 'fetchPendingPromotionsList'])->middleware(['checkLogin'])->name('fetchPendingPromotionsList');
Route::post('fetchApprovedPromotionsList', [PromotionController::class, 'fetchApprovedPromotionsList'])->middleware(['checkLogin'])->name('fetchApprovedPromotionsList');
Route::post('fetchRejectedPromotionsList', [PromotionController::class, 'fetchRejectedPromotionsList'])->middleware(['checkLogin'])->name('fetchRejectedPromotionsList');

// Promo Codes
Route::get('promoCodes', [PromoCodeController::class, 'promoCodes'])->middleware(['checkLogin'])->name('promoCodes');
Route::post('fetchAllPromoCodesList', [PromoCodeController::class, 'fetchAllPromoCodesList'])->middleware(['checkLogin'])->name('fetchAllPromoCodesList');
Route::post('addPromoCodeItem', [PromoCodeController::class, 'addPromoCodeItem'])->middleware(['checkLogin'])->name('addPromoCodeItem');
Route::post('editPromoCodeItem', [PromoCodeController::class, 'editPromoCodeItem'])->middleware(['checkLogin'])->name('editPromoCodeItem');
Route::get('deletePromoCode/{id}', [PromoCodeController::class, 'deletePromoCode'])->middleware(['checkLogin'])->name('deletePromoCode');

// View Appointment
Route::get('viewAppointment/{id}', [AppointmentController::class, 'viewAppointment'])->middleware(['checkLogin'])->name('viewAppointment');

// View Doctor
Route::get('viewDoctorProfile/{id}', [DoctorController::class, 'viewDoctorProfile'])->middleware(['checkLogin'])->name('viewDoctorProfile');
Route::get('deleteDoctorAccounting/{id}', [DoctorController::class, 'deleteDoctorAccounting'])->middleware(['checkLogin'])->name('deleteDoctorAccounting');
Route::get('banDoctor/{id}', [DoctorController::class, 'banDoctor'])->middleware(['checkLogin'])->name('banDoctor');
Route::get('activateDoctor/{id}', [DoctorController::class, 'activateDoctor'])->middleware(['checkLogin'])->name('activateDoctor');
Route::post('addDoctor_Admin', [DoctorController::class, 'addDoctor_Admin'])->middleware(['checkLogin'])->name('addDoctor_Admin');
Route::post('updateDoctorDetails_Admin', [DoctorController::class, 'updateDoctorDetails_Admin'])->middleware(['checkLogin'])->name('updateDoctorDetails_Admin');
Route::post('fetchDoctorAppointmentsList', [DoctorController::class, 'fetchDoctorAppointmentsList'])->middleware(['checkLogin'])->name('fetchDoctorAppointmentsList');
Route::post('fetchDoctorReviewsList', [DoctorController::class, 'fetchDoctorReviewsList'])->middleware(['checkLogin'])->name('fetchDoctorReviewsList');
Route::post('fetchDoctorWalletStatement', [DoctorController::class, 'fetchDoctorWalletStatement'])->middleware(['checkLogin'])->name('fetchDoctorWalletStatement');
Route::post('fetchDoctorPayoutRequestsList', [DoctorController::class, 'fetchDoctorPayoutRequestsList'])->middleware(['checkLogin'])->name('fetchDoctorPayoutRequestsList');
Route::post('fetchDoctorEarningsList', [DoctorController::class, 'fetchDoctorEarningsList'])->middleware(['checkLogin'])->name('fetchDoctorEarningsList');
Route::post('fetchDoctorServicesList', [DoctorController::class, 'fetchDoctorServicesList'])->middleware(['checkLogin'])->name('fetchDoctorServicesList');
Route::get('deleteService/{id}', [DoctorController::class, 'deleteService'])->middleware(['checkLogin'])->name('deleteService');
Route::post('fetchDoctorExpertiseList', [DoctorController::class, 'fetchDoctorExpertiseList'])->middleware(['checkLogin'])->name('fetchDoctorExpertiseList');
Route::get('deleteExpertise/{id}', [DoctorController::class, 'deleteExpertise'])->middleware(['checkLogin'])->name('deleteExpertise');
Route::post('fetchDoctorServiceLocationList', [DoctorController::class, 'fetchDoctorServiceLocationList'])->middleware(['checkLogin'])->name('fetchDoctorServiceLocationList');
Route::get('deleteServiceLocation/{id}', [DoctorController::class, 'deleteServiceLocation'])->middleware(['checkLogin'])->name('deleteServiceLocation');
Route::post('fetchDoctorExperienceList', [DoctorController::class, 'fetchDoctorExperienceList'])->middleware(['checkLogin'])->name('fetchDoctorExperienceList');
Route::get('deleteExperience/{id}', [DoctorController::class, 'deleteExperience'])->middleware(['checkLogin'])->name('deleteExperience');
Route::post('fetchDoctorAwardsList', [DoctorController::class, 'fetchDoctorAwardsList'])->middleware(['checkLogin'])->name('fetchDoctorAwardsList');
Route::get('deleteAwards/{id}', [DoctorController::class, 'deleteAwards'])->middleware(['checkLogin'])->name('deleteAwards');
Route::post('fetchDoctorHolidaysList', [DoctorController::class, 'fetchDoctorHolidaysList'])->middleware(['checkLogin'])->name('fetchDoctorHolidaysList');
Route::get('deleteDoctorHoliday/{id}', [DoctorController::class, 'deleteDoctorHoliday'])->middleware(['checkLogin'])->name('deleteDoctorHoliday');

// View Appointment
Route::get('viewAppointment/{id}', [AppointmentController::class, 'viewAppointment'])->middleware(['checkLogin'])->name('viewAppointment');

// View User
Route::get('viewUserProfile/{id}', [UsersController::class, 'viewUserProfile'])->middleware(['checkLogin'])->name('viewUserProfile');
Route::post('fetchUserAppointmentsList', [UsersController::class, 'fetchUserAppointmentsList'])->middleware(['checkLogin'])->name('fetchUserAppointmentsList');
Route::post('fetchUserWalletStatementList', [UsersController::class, 'fetchUserWalletStatementList'])->middleware(['checkLogin'])->name('fetchUserWalletStatementList');
Route::post('fetchUserWithdrawRequestsList', [UsersController::class, 'fetchUserWithdrawRequestsList'])->middleware(['checkLogin'])->name('fetchUserWithdrawRequestsList');
Route::post('fetchUserWalletRechargeLogsList', [UsersController::class, 'fetchUserWalletRechargeLogsList'])->middleware(['checkLogin'])->name('fetchUserWalletRechargeLogsList');
Route::post('fetchUserPatientsList', [UsersController::class, 'fetchUserPatientsList'])->middleware(['checkLogin'])->name('fetchUserPatientsList');
Route::post('rechargeWalletFromAdmin', [UsersController::class, 'addMoneyToUserWallet'])->middleware(['checkLogin'])->name('rechargeWalletFromAdmin');


// Coupons
Route::get('coupons', [SettingsController::class, 'coupons'])->middleware(['checkLogin'])->name('coupons');
Route::post('fetchAllCouponsList', [SettingsController::class, 'fetchAllCouponsList'])->middleware(['checkLogin'])->name('fetchAllCouponsList');
Route::post('addCouponItem', [SettingsController::class, 'addCouponItem'])->middleware(['checkLogin'])->name('addCouponItem');
Route::post('editCouponItem', [SettingsController::class, 'editCouponItem'])->middleware(['checkLogin'])->name('editCouponItem');
Route::get('deleteCoupon/{id}', [SettingsController::class, 'deleteCoupon'])->middleware(['checkLogin'])->name('deleteCoupon');


//Interest
Route::get('interests', [SettingsController::class, 'interests'])->middleware(['checkLogin'])->name('interests');
Route::post('fetchAllInterestsList', [SettingsController::class, 'fetchAllInterestsList'])->middleware(['checkLogin'])->name('fetchAllInterestsList');
Route::post('addInterestItem', [SettingsController::class, 'addInterestItem'])->middleware(['checkLogin'])->name('addInterestItem');
Route::post('editInterestItem', [SettingsController::class, 'editInterestItem'])->middleware(['checkLogin'])->name('editInterestItem');
Route::get('deleteInterest/{id}', [SettingsController::class, 'deleteInterest'])->middleware(['checkLogin'])->name('deleteInterest');


//slider
Route::get('sliders', [SettingsController::class, 'sliders'])->middleware(['checkLogin'])->name('sliders');
Route::post('fetchAllslidersList', [SettingsController::class, 'fetchAllslidersList'])->middleware(['checkLogin'])->name('fetchAllslidersList');
Route::post('addsliderItem', [SettingsController::class, 'addsliderItem'])->middleware(['checkLogin'])->name('addsliderItem');
Route::post('editsliderItem', [SettingsController::class, 'editsliderItem'])->middleware(['checkLogin'])->name('editsliderItem');
Route::get('deleteslider/{id}', [SettingsController::class, 'deleteslider'])->middleware(['checkLogin'])->name('deleteslider');


//JobTitles
Route::get('JobTitles', [SettingsController::class, 'JobTitles'])->middleware(['checkLogin'])->name('jobTitles');
Route::post('fetchAllJobTitlesList', [SettingsController::class, 'fetchAllJobTitlesList'])->middleware(['checkLogin'])->name('fetchAlljobTitlesList');
Route::post('addJobTitlesItem', [SettingsController::class, 'addJobTitlesItem'])->middleware(['checkLogin'])->name('addJobTitlesItem');
Route::post('editJobTitlesItem', [SettingsController::class, 'editJobTitlesItem'])->middleware(['checkLogin'])->name('editJobTitlesItem');
Route::get('deleteJobTitles/{id}', [SettingsController::class, 'deleteJobTitles'])->middleware(['checkLogin'])->name('deleteJobTitles');


// Reviews
Route::get('reviews', [SettingsController::class, 'reviews'])->middleware(['checkLogin'])->name('reviews');
Route::post('fetchAllReviewsList', [SettingsController::class, 'fetchAllReviewsList'])->middleware(['checkLogin'])->name('fetchAllReviewsList');
Route::get('deleteReview/{id}', [SettingsController::class, 'deleteReview'])->middleware(['checkLogin'])->name('deleteReview');

// Faqs
Route::get('faqs', [SettingsController::class, 'faqs'])->middleware(['checkLogin'])->name('faqs');
Route::post('fetchFaqCatsList', [SettingsController::class, 'fetchFaqCatsList'])->middleware(['checkLogin'])->name('fetchFaqCatsList');
Route::post('addFaqCategory', [SettingsController::class, 'addFaqCategory'])->middleware(['checkLogin'])->name('addFaqCategory');
Route::post('editFaqCategory', [SettingsController::class, 'editFaqCategory'])->middleware(['checkLogin'])->name('editFaqCategory');
Route::get('deleteFaqCat/{id}', [SettingsController::class, 'deleteFaqCat'])->middleware(['checkLogin'])->name('deleteFaqCat');
Route::post('addFaq', [SettingsController::class, 'addFaq'])->middleware(['checkLogin'])->name('addFaq');
Route::post('fetchFaqList', [SettingsController::class, 'fetchFaqList'])->middleware(['checkLogin'])->name('fetchFaqList');
Route::get('deleteFaq/{id}', [SettingsController::class, 'deleteFaq'])->middleware(['checkLogin'])->name('deleteFaq');
Route::get('getFaqCats', [SettingsController::class, 'getFaqCats'])->middleware(['checkLogin'])->name('getFaqCats');
Route::post('editFaq', [SettingsController::class, 'editFaq'])->middleware(['checkLogin'])->name('editFaq');

// Platform Earning History
Route::get('platformEarnings', [SettingsController::class, 'platformEarnings'])->middleware(['checkLogin'])->name('platformEarnings');
Route::post('fetchPlatformEarningsList', [SettingsController::class, 'fetchPlatformEarningsList'])->middleware(['checkLogin'])->name('fetchPlatformEarningsList');
Route::get('deletePlatformEarningItem/{id}', [SettingsController::class, 'deletePlatformEarningItem'])->middleware(['checkLogin'])->name('deletePlatformEarningItem');

// Wallet recharge (user)
Route::get('userWalletRecharge', [SettingsController::class, 'userWalletRecharge'])->middleware(['checkLogin'])->name('userWalletRecharge');
Route::post('fetchWalletRechargeList', [SettingsController::class, 'fetchWalletRechargeList'])->middleware(['checkLogin'])->name('fetchWalletRechargeList');


// Banners
Route::get('banners', [SettingsController::class, 'banners'])->middleware(['checkLogin'])->name('banners');
Route::post('fetchBannersList', [SettingsController::class, 'fetchBannersList'])->middleware(['checkLogin'])->name('fetchBannersList');
Route::post('addBanner', [SettingsController::class, 'addBanner'])->middleware(['checkLogin'])->name('addBanner');
Route::get('deleteBanner/{id}', [SettingsController::class, 'deleteBanner'])->middleware(['checkLogin'])->name('deleteBanner');

// Notifications
Route::get('notifications', [SettingsController::class, 'notifications'])->middleware(['checkLogin'])->name('notifications');
Route::post('fetchUserNotificationList', [SettingsController::class, 'fetchUserNotificationList'])->middleware(['checkLogin'])->name('fetchUserNotificationList');
Route::get('deleteUserNotification/{id}', [SettingsController::class, 'deleteUserNotification'])->middleware(['checkLogin'])->name('deleteUserNotification');
Route::post('addUserNotification', [SettingsController::class, 'addUserNotification'])->middleware(['checkLogin'])->name('addUserNotification');
Route::post('editUserNotification', [SettingsController::class, 'editUserNotification'])->middleware(['checkLogin'])->name('editUserNotification');

Route::post('addDoctorNotification', [SettingsController::class, 'addDoctorNotification'])->middleware(['checkLogin'])->name('addDoctorNotification');
Route::post('fetchDoctorNotificationList', [SettingsController::class, 'fetchDoctorNotificationList'])->middleware(['checkLogin'])->name('fetchDoctorNotificationList');
Route::get('deleteDoctorNotification/{id}', [SettingsController::class, 'deleteDoctorNotification'])->middleware(['checkLogin'])->name('deleteDoctorNotification');
Route::post('editDoctorNotification', [SettingsController::class, 'editDoctorNotification'])->middleware(['checkLogin'])->name('editDoctorNotification');

Route::post('addUserAndDoctorNotification', [SettingsController::class, 'addUserAndDoctorNotification'])->middleware(['checkLogin'])->name('addUserAndDoctorNotification');

// AI
Route::get('ai', [AiController::class, 'ai'])->middleware(['checkLogin'])->name('ai');
Route::post('fetchQuestionsList', [AiController::class, 'fetchQuestionsList'])->middleware(['checkLogin'])->name('fetchQuestionsList');
Route::post('addQuestion', [AiController::class, 'addQuestion'])->middleware(['checkLogin'])->name('addQuestion');
Route::post('editQuestion', [AiController::class, 'editQuestion'])->middleware(['checkLogin'])->name('editQuestion');
Route::get('deleteQuestion/{id}', [AiController::class, 'deleteQuestion'])->middleware(['checkLogin'])->name('deleteQuestion');

// Articles
Route::get('articles', [ArticleController::class, 'articles'])->middleware(['checkLogin'])->name('articles');
Route::post('fetchArticlesList', [ArticleController::class, 'fetchArticlesList'])->middleware(['checkLogin'])->name('fetchArticlesList');
Route::post('addArticle', [ArticleController::class, 'addArticle'])->middleware(['checkLogin'])->name('addArticle');
Route::post('editArticle', [ArticleController::class, 'editArticle'])->middleware(['checkLogin'])->name('editArticle');
Route::get('deleteArticle/{id}', [ArticleController::class, 'deleteArticle'])->middleware(['checkLogin'])->name('deleteArticle');

// Packages
Route::get('packages', [PackageController::class, 'packages'])->middleware(['checkLogin'])->name('packages');
Route::post('fetchPackagesList', [PackageController::class, 'fetchPackagesList'])->middleware(['checkLogin'])->name('fetchPackagesList');
Route::post('addPackage', [PackageController::class, 'addPackage'])->middleware(['checkLogin'])->name('addPackage');
Route::post('editPackage', [PackageController::class, 'editPackage'])->middleware(['checkLogin'])->name('editPackage');
Route::get('deletePackage/{id}', [PackageController::class, 'deletePackage'])->middleware(['checkLogin'])->name('deletePackage');

// Contacts
Route::get('contacts', [ContactController::class, 'contacts'])->middleware(['checkLogin'])->name('contacts');
Route::post('fetchContactsList', [ContactController::class, 'fetchContactsList'])->middleware(['checkLogin'])->name('fetchContactsList');
Route::post('addContact', [ContactController::class, 'addContact'])->middleware(['checkLogin'])->name('addContact');
Route::post('editContact', [ContactController::class, 'editContact'])->middleware(['checkLogin'])->name('editContact');
Route::get('deleteContact/{id}', [ContactController::class, 'deleteContact'])->middleware(['checkLogin'])->name('deleteContact');


// User Withdrawals
Route::get('userWithdraws', [UsersController::class, 'userWithdraws'])->middleware(['checkLogin'])->name('userWithdraws');
Route::post('fetchUserPendingWithdrawalsList', [UsersController::class, 'fetchUserPendingWithdrawalsList'])->middleware(['checkLogin'])->name('fetchUserPendingWithdrawalsList');
Route::post('fetchUserCompletedWithdrawalsList', [UsersController::class, 'fetchUserCompletedWithdrawalsList'])->middleware(['checkLogin'])->name('fetchUserCompletedWithdrawalsList');
Route::post('fetchUserRejectedWithdrawalsList', [UsersController::class, 'fetchUserRejectedWithdrawalsList'])->middleware(['checkLogin'])->name('fetchUserRejectedWithdrawalsList');
Route::post('completeUserWithdrawal', [UsersController::class, 'completeUserWithdrawal'])->middleware(['checkLogin'])->name('completeUserWithdrawal');
Route::post('rejectUserWithdrawal', [UsersController::class, 'rejectUserWithdrawal'])->middleware(['checkLogin'])->name('rejectUserWithdrawal');

// Doctor Withdrawal
Route::get('doctorWithdraws', [DoctorController::class, 'doctorWithdraws'])->middleware(['checkLogin'])->name('doctorWithdraws');
Route::post('fetchDoctorPendingWithdrawalsList', [DoctorController::class, 'fetchDoctorPendingWithdrawalsList'])->middleware(['checkLogin'])->name('fetchDoctorPendingWithdrawalsList');
Route::post('fetchDoctorCompletedWithdrawalsList', [DoctorController::class, 'fetchDoctorCompletedWithdrawalsList'])->middleware(['checkLogin'])->name('fetchDoctorCompletedWithdrawalsList');
Route::post('fetchDoctorRejectedWithdrawalsList', [DoctorController::class, 'fetchDoctorRejectedWithdrawalsList'])->middleware(['checkLogin'])->name('fetchDoctorRejectedWithdrawalsList');
Route::post('completeDoctorWithdrawal', [DoctorController::class, 'completeDoctorWithdrawal'])->middleware(['checkLogin'])->name('completeDoctorWithdrawal');
Route::post('rejectDoctorWithdrawal', [DoctorController::class, 'rejectDoctorWithdrawal'])->middleware(['checkLogin'])->name('rejectDoctorWithdrawal');

// Doctor Categories
Route::get('doctorCategories', [SettingsController::class, 'doctorCategories'])->middleware(['checkLogin'])->name('doctorCategories');
Route::post('fetchDoctorCatsList', [SettingsController::class, 'fetchDoctorCatsList'])->middleware(['checkLogin'])->name('fetchDoctorCatsList');
Route::post('addDoctorCat', [SettingsController::class, 'addDoctorCat'])->middleware(['checkLogin'])->name('addDoctorCat');
Route::post('editDoctorCat', [SettingsController::class, 'editDoctorCat'])->middleware(['checkLogin'])->name('editDoctorCat');
Route::get('deleteDoctorCat/{id}', [SettingsController::class, 'deleteDoctorCat'])->middleware(['checkLogin'])->name('deleteDoctorCat');
Route::post('fetchDoctorCatSuggestionsList', [SettingsController::class, 'fetchDoctorCatSuggestionsList'])->middleware(['checkLogin'])->name('fetchDoctorCatSuggestionsList');
Route::get('deleteDoctorCatSuggestion/{id}', [SettingsController::class, 'deleteDoctorCatSuggestion'])->middleware(['checkLogin'])->name('deleteDoctorCatSuggestion');

// Settings
Route::get('settings', [SettingsController::class, 'settings'])->middleware(['checkLogin'])->name('settings');
Route::post('updateGlobalSettings', [SettingsController::class, 'updateGlobalSettings'])->middleware(['checkLogin'])->name('updateGlobalSettings');
Route::post('changePassword', [SettingsController::class, 'changePassword'])->middleware(['checkLogin'])->name('changePassword');
Route::post('updatePaymentSettings', [SettingsController::class, 'updatePaymentSettings'])->middleware(['checkLogin'])->name('updatePaymentSettings');
Route::post('fetchAllTaxList', [SettingsController::class, 'fetchAllTaxList'])->middleware(['checkLogin'])->name('fetchAllTaxList');
Route::post('addTaxItem', [SettingsController::class, 'addTaxItem'])->middleware(['checkLogin'])->name('addTaxItem');
Route::post('editTaxItem', [SettingsController::class, 'editTaxItem'])->middleware(['checkLogin'])->name('editTaxItem');
Route::get('deleteTaxItem/{id}', [SettingsController::class, 'deleteTaxItem'])->middleware(['checkLogin'])->name('deleteTaxItem');
Route::get('changeTaxStatus/{id}/{value}', [SettingsController::class, 'changeTaxStatus'])->middleware(['checkLogin'])->name('changeTaxStatus');



// Pages Routes
Route::get('viewPrivacy', [PagesController::class, 'viewPrivacy'])->middleware(['checkLogin'])->name('viewPrivacy');
Route::post('updatePrivacy', [PagesController::class, 'updatePrivacy'])->middleware(['checkLogin'])->name('updatePrivacy');
Route::get('viewTerms', [PagesController::class, 'viewTerms'])->middleware(['checkLogin'])->name('viewTerms');
Route::post('updateTerms', [PagesController::class, 'updateTerms'])->middleware(['checkLogin'])->name('updateTerms');
Route::get('privacypolicy', [PagesController::class, 'privacypolicy'])->name('privacypolicy');
Route::get('termsOfUse', [PagesController::class, 'termsOfUse'])->name('termsOfUse');

// Cleanup Routes
Route::get('cleanDatabase', [SettingsController::class, 'cleanDatabase'])->name('cleanDatabase');


// Adds
Route::get('/adds/update-loyalty-points', [LoyaltyPointsController::class, 'index'])->name('loyaltyPoints.index');
Route::put('/adds/update-loyalty-points', [LoyaltyPointsController::class, 'updateLoyaltyPoints'])->name('loyaltyPoints.updateLoyaltyPoints');

