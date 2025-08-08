<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\api\BundleController;
use App\Http\Controllers\api\CommissionSettingController;
use App\Http\Controllers\api\QuizReattemptAppealController;
use App\Http\Controllers\api\ReferralController;
use App\Http\Controllers\api\User\PermissionController;
use App\Http\Controllers\api\User\RoleController;
use App\Http\Controllers\api\User\StudentController;
use App\Http\Controllers\api\Auth\LoginController;
use App\Http\Controllers\api\Auth\RegisterController;
use App\Http\Controllers\api\User\UserController;
use App\Http\Controllers\api\Auth\ForgotPasswordController;
use App\Http\Controllers\api\Auth\ResetPasswordController;
use App\Http\Controllers\api\Auth\VerifyEmailController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\api\CertificateController;
use App\Http\Controllers\api\CommissionController;
use App\Http\Controllers\api\CourseController;
use App\Http\Controllers\api\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\api\AffiliateDashboardController;
use App\Http\Controllers\api\Auth\SocialAuthController;
use App\Http\Controllers\api\GrantCodeController;
use App\Http\Controllers\api\LessonController;
use App\Http\Controllers\api\LessonProgressController;
use App\Http\Controllers\api\QuizController;
use App\Http\Controllers\api\ModuleController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\StudentGradeController;
use App\Http\Controllers\api\User\AffiliateController;
use App\Http\Controllers\api\WebhookController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\api\WalletController;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

route::prefix('auth')->group(function(){
    Route::get('google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('google/login', [SocialAuthController::class, 'loginWithGoogle'])->name('api.google.login');
});

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class,'register']);
Route::get('email/verify',[VerifyEmailController::class,'verify'])->name('verification.verify');
Route::post('email/verify/resend',[VerifyEmailController::class,'resend'])->name('verification.resend');
Route::get('payment/stripe/success',[PaymentController::class,'stripeSuccess']);
Route::post('payment/stripe/cancel',[PaymentController::class,'stripeCancel']);
Route::get('certification/dual-award',[ModuleController::class,'showModuleAtLandingPage']);
Route::post('payment/stripe/success',[PaymentController::class,'stripeSuccess']);
Route::prefix('password')->group(function () {
    Route::post('email',[ForgotPasswordController::class,'sendResetLinkEmail']);
    Route::post('reset',[ResetPasswordController::class,'resetPassword'])->name('password-reset');
    // Route::get('reset',function(Request $request){
    //     return dd($request->all());
    // });
});



Route::get('course/list',[CourseController::class,'list']);
Route::get('admin/module/list',[ModuleController::class,'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    // Add other protected routes here if needed
});

// Route::middleware(['auth:sanctum','role:admin|agent'])->prefix('user')->group(function(){
Route::middleware(['auth:sanctum'])->prefix('user')->group(function(){
    Route::get('list', [UserController::class,'index']);
    Route::post('create', [UserController::class,'store']);
    Route::post('register',[UserController::class,'registerUser']);
    Route::get('{id}/show', [UserController::class,'show']);
    Route::post('{id}/update', [UserController::class,'update']);
    Route::delete('{id}/delete', [UserController::class,'destroy']);
    Route::get('purchase-items',[UserController::class,'getPurchaseItems']);
    Route::post('list/role',[UserController::class,'getUserListByRole']);
    Route::post('{id}/role/assign',[UserController::class,'roleAssign']);
    Route::post('reset/admin-pw',[ResetPasswordController::class,'resetAdminPw']);
    Route::post('{id}/change/password',[UserController::class,'changePassword']);
    Route::post('affiliate/apply',[AffiliateController::class,'applyAffiliate']);
});

Route::middleware(['auth:sanctum'])->prefix('student')->group(function(){
    Route::get('list/download',[StudentController::class,'downloadExcel']);
    Route::get('list',[StudentController::class,'index']);
    Route::get('get/{id}',[StudentController::class,'show']);
    Route::post('create',[StudentController::class,'store']);
    Route::post('update',[StudentController::class,'update']);
    Route::delete('{id}/delete',[StudentController::class,'destroy']);
    Route::post('enroll_payment',[PaymentController::class,'studentEnrollPayments']);
    Route::get('grant',[StudentController::class,'grantList']);
    Route::post('grant/confirm',[StudentController::class,'grantConfirmation']);
    Route::get('grant/history',[StudentController::class,'grantHistory']);
    Route::get('search',[StudentController::class,'search']);
});

// Route::middleware(['auth:sanctum'])->resource('affiliate',AffiliateController::class);
Route::middleware(['auth:sanctum'])->prefix('affiliate')->group(function(){
    // Route::resource('',AffiliateController::class);
    Route::post('create',[AffiliateController::class,'store']);
    Route::post('{id}/update',[AffiliateController::class,'update']);
    Route::delete('{id}/delete',[AffiliateController::class,'destroy']);
    Route::get('list',[AffiliateController::class,'index']);
    Route::get('get/{id}',[AffiliateController::class,'show']);
    Route::get('student',[AffiliateController::class,'getStudentList']);
});

Route::middleware(['auth:sanctum'])->prefix('admin/affiliate')->group(function(){
    Route::get('assign-list',[AffiliateController::class,'getUserList']);
    Route::get('assign-list/download',[AffiliateController::class,'downloadAffiliateAssigneeList']);
    Route::get('search-user',[AffiliateController::class,'searchInUserList']);
    Route::get('search-agent',[AffiliateController::class,'searchAffiliate']);
    Route::get('applies-list',[AffiliateController::class,'getAffiliateApplications']);
    Route::get('applies/reject/list',[AffiliateController::class,'getAffiliateApplicationsRejectList']);
    Route::post('confirmation/{id}',[AffiliateController::class,'confirmationAffiliateApplication']);
});

Route::middleware(['auth:sanctum'])->prefix('referral')->group(function(){
    Route::get('list', [ReferralController::class,'list']);
});

Route::middleware(['auth:sanctum'])->prefix('commission')->group(function(){
    Route::get('list', [CommissionController::class,'index']);
    Route::post('create', [CommissionController::class,'store']);
});

Route::middleware(['auth:sanctum'])->prefix('commission/setting')->group(function(){
    Route::get('list', [CommissionSettingController::class,'index']);
    Route::get('{id}/get',[CommissionSettingController::class,'show']);
    Route::post('create',[CommissionSettingController::class,'store']);
    Route::post('{id}/update',[CommissionSettingController::class,'update']);
    Route::delete('{id}/delete', [CommissionSettingController::class,'destroy']);
});

Route::middleware(['auth:sanctum'])->prefix('role')->group(function(){
    Route::get('list', [RoleController::class,'index']);
    Route::post('get',[RoleController::class,'show']);
    Route::post('create', [RoleController::class,'store']);
    Route::post('{id}/update', [RoleController::class,'update']);
    Route::delete('{id}/delete', [RoleController::class,'destroy']);
});

Route::middleware(['auth:sanctum'])->prefix('permission')->group(function(){
    Route::get('list', [PermissionController::class,'index']);
    Route::post('assign-role',[PermissionController::class,'assignToRole']);
    Route::post('assign-user',[PermissionController::class,'assignToUser']);
});

// Route::middleware(['auth:sanctum','role:admin|agent'])->resource('bundle', BundleController::class);
Route::middleware(['auth:sanctum'])->prefix('bundle')->group(function(){
    Route::get('', [BundleController::class,'index']);
    Route::get('{bundle}',[BundleController::class,'show']);
    Route::post('', [BundleController::class,'store']);
    Route::post('{bundle}/update', [BundleController::class,'update']);
    Route::delete('{bundle}/delete', [BundleController::class,'destroy']);
});

// Route::middleware(['auth:sanctum','role:admin|agent'])->resource('course', CourseController::class);
Route::middleware(['auth:sanctum'])->prefix('course')->group(function(){
    Route::get('', [CourseController::class,'index']);
    Route::get('{course}',[CourseController::class,'show']);
    Route::post('', [CourseController::class,'store']);
    Route::post('{course}/update', [CourseController::class,'update']);
    Route::delete('{course}/delete', [CourseController::class,'destroy']);
    // Route::post('enroll',[CourseController::class,'selfEnrollCourse']);
    Route::get('user/{user}',[CourseController::class,'getCourseByUserId']);
    ROute::post('progress',[CourseController::class,'updateCourseProgress']);
});

Route::middleware(['auth:sanctum'])->resource('module',ModuleController::class);
Route::middleware(['auth:sanctum'])->prefix('module')->group(function(){
    Route::post('{module}/update',[ModuleController::class,'update']);
    Route::post('course', [ModuleController::class,'getByCourse']);
});

Route::middleware(['auth:sanctum'])->resource('quiz',QuizController::class);
Route::middleware(['auth:sanctum'])->prefix('quiz')->group(function(){
    Route::get('lesson/{lesson_id}',[QuizController::class,'listByLesson']);
    Route::post('{quiz_id}/update',[QuizController::class,'update']);
    Route::post('submit', [QuizController::class,'submitAnswers']);
    Route::post('start',[QuizController::class,'startQuiz']);
    Route::get('re-attempt/get',[QuizReattemptAppealController::class,'index']);
    Route::post('re-attempt/appeal',[QuizReattemptAppealController::class,'store']);
    Route::post('re-attempt/appeal/confirm',[QuizReattemptAppealController::class,'approveRequest']);
    
});

Route::middleware(['auth:sanctum'])->post('content-image-upload',[LessonController::class,'uploadContentImage']);
Route::middleware(['auth:sanctum'])->resource('lesson',LessonController::class);
Route::middleware(['auth:sanctum'])->prefix('lesson')->group(function(){
    Route::post('{lesson}/update',[LessonController::class,'update']);
    Route::post('module', [LessonController::class,'getByModule']);
    Route::post('update-order',[LessonController::class,'updateSortingOrder']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('add-to-cart',[CartController::class,'addToCart']);
    Route::post('remove-cart', [CartController::class,'removeCart']);
    Route::get('getCart',[CartController::class,'getShoppingCart']);
    Route::get('clear-cart',[CartController::class,'clearCart']);
    Route::post('cart/checkout',[CartController::class,'checkout']);
});

Route::middleware('auth:sanctum')->resource('order',OrderController::class);
Route::middleware('auth:sanctum')->prefix('order')->group(function(){
    Route::get('list/download',[OrderController::class,'downloadOrderList']);
    Route::get('user/{user}',[OrderController::class,'getUserOrder']);
    Route::post('create',[OrderController::class,'store']);
    Route::post('{order}/update',[OrderController::class,'update']);
    Route::delete('{order}/delete',[OrderController::class,'destroy']);
});

Route::middleware('auth:sanctum')->resource('payment',PaymentController::class);
Route::middleware('auth:sanctum')->prefix('payment')->group(function(){
    Route::get('user/{user}',[PaymentController::class,'listByUser']);
    Route::post('confirm',[PaymentController::class,'confirmPayment']);
    Route::post('reject',[PaymentController::class,'rejectPayment']);
    Route::post('stripe/success',[PaymentController::class,'stripeSuccess']);
    Route::post('stripe/cancel',[PaymentController::class,'stripeCancel']);
    Route::get('check/transactionhash',[PaymentController::class,'getTransactionStatus']);
});

Route::middleware('auth:sanctum')->resource('grades',StudentGradeController::class);
Route::middleware('auth:sanctum')->prefix('grades')->group(function(){
    Route::post('student',[StudentGradeController::class,'getStudentGrades']);
});

Route::middleware('auth:sanctum')->prefix('progress')->group(function(){
    Route::post('lesson/create',[LessonProgressController::class,'store']);
    Route::post('lesson/update',[LessonProgressController::class,'update']);
    Route::post('lesson/module',[LessonProgressController::class,'getLessonProgressByModule']);
    Route::get('lesson/percentage',[LessonProgressController::class,'getProgressPercentageByModule']);
});

// Route::middleware('auth:sanctum')->resource('notification',NotificationController::class);
Route::middleware('auth:sanctum')->prefix('notification')->group(function(){
    // Route::get('get', [NotificationController::class, 'index']);
    Route::get('', [NotificationController::class, 'index']);
    Route::get('unread/count', [NotificationController::class, 'unreadCounts']);
    Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('read/all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('{id}', [NotificationController::class, 'destroy']);
    Route::post('test', [NotificationController::class, 'testNotification']);
});

Route::middleware('auth:sanctum')->post('pusher/auth', [NotificationController::class, 'pusherAuth']);

Route::middleware('auth:sanctum')->prefix('admin/student-certificate')->group(function(){
    Route::get('list',[CertificateController::class,'studentCertificateList']);
    Route::post('create',[CertificateController::class,'store']);
    Route::get('{id}/show',[CertificateController::class,'show']);
    Route::post('{id}/update',[CertificateController::class,'update']);
    Route::delete('{id}/delete',[CertificateController::class,'destroy']);
    Route::post('{id}/upload',[CertificateController::class,'uploadCertificate']);
    Route::post('{id}/download',[CertificateController::class,'downloadCertificate']);
});

Route::middleware('auth:sanctum')->prefix('student/certificate')->group(function(){
    Route::get('list',[CertificateController::class,'getCertificatesByStudent']);
    Route::post('{id}/download',[CertificateController::class,'downloadCertificate']);
});

Route::middleware('auth:sanctum')->prefix('dashboard')->group(function(){
    Route::get('current-program',[StudentDashboardController::class,'currentProgram']);
});

Route::middleware('auth:sanctum')->prefix('affiliate/dashboard')->group(function(){
    Route::get('',[AffiliateDashboardController::class,'dashboard']);
});

Route::middleware('auth:sanctum')->prefix('admin/dashboard')->group(function(){
    Route::get('',[AdminDashboardController::class,'index']);
    Route::get('student-count',[AdminDashboardController::class,'studentCount']);
    Route::get('course-count',[AdminDashboardController::class,'courseCount']);
    Route::get('module-count',[AdminDashboardController::class,'moduleCount']);
    Route::get('lesson-count',[AdminDashboardController::class,'lessonCount']);
    Route::get('order-count',[AdminDashboardController::class,'orderCount']);
    Route::get('affiliate-count',[AdminDashboardController::class,'affiliateCount']);
});

Route::middleware('auth:sanctum')->prefix('report')->group(function(){
    Route::post('student',[StudentController::class,'getViaCreateDate']);
    Route::post('course',[CourseController::class,'courseReport']);
    Route::post('module',[ModuleController::class,'moduleReport']);
    Route::post('lesson',[LessonController::class,'lessonReport']);
    Route::post('quiz',[QuizController::class,'quizReport']);
    Route::post('order',[OrderController::class,'orderReport']);
    Route::post('payment',[PaymentController::class,'paymentReport']);
    Route::post('commission',[CommissionController::class,'commissionReport']);
});

// Wallet routes
Route::middleware(['auth:sanctum'])->prefix('wallet')->group(function() {
    Route::get('/', [WalletController::class, 'index']);
    Route::post('/', [WalletController::class, 'store']);
    Route::post('/deposit', [WalletController::class, 'deposit']);
    Route::post('/withdraw', [WalletController::class, 'withdraw']);
    Route::get('/transactions', [WalletController::class, 'transactions']);
    Route::get('/balance', [WalletController::class, 'balance']);
});

Route::middleware('auth:sanctum')->post('grant-code/check',[GrantCodeController::class,'showGrantInfo']);

Route::post('/webhook/stripe', [WebhookController::class, 'handleWebhook']);

Route::middleware('auth:sanctum')->get('/key-info', function (Request $request) {
    $stripeSecret = config('services.stripe.secret');
    return response()->json([
        'data' => $stripeSecret,
        'status' => 200
    ],200);
});

Route::middleware('auth:sanctum')->get('/auth-token', function (Request $request) {
    return response()->json([
        'data' => Auth::user(),
        'status' => 200
    ],200);
});

Route::get('receipt-template',function(){
    return view('receipt-template-test');
});
Route::get('test-noti',function(){
    return view('test-notification');
});

Route::get('test/student/download-csv',[StudentController::class,'downloadExcel']);
Route::get('test/affiliate/download-csv',[AffiliateController::class,'downloadAffiliateAssigneeList']);
Route::get('quiz-fail/list',[QuizReattemptAppealController::class,'getReattemptedCountOverList']);


