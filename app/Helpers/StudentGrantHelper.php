<?php
namespace App\Helpers;

use App\Interfaces\StudentRepositoryInterface;
use App\Models\User;
use App\Models\UserGrantCode;
use App\Notifications\StudentGrantNotification;
use Stripe\StripeClient;
use function App\Helpers\generateUniqueCode;

class StudentGrantHelper
{
    protected $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }

    public function grantConfirmation($data)
    {
        $student = $this->studentRepositoryInterface->grantConfirmation($data);

        if ($student) {
            $stripe = new StripeClient(config('services.stripe.secret'));
            if ($stripe) {
                $code = $stripe->coupons->create([
                    'percent_off' => UserGrantCode::DEFAULT_GRANT_AMOUNT,
                    'duration' => 'once',
                    'name' => $student->user ? $student->user->name : 'Student Grant - ' . $student->id,
                    'currency' => 'usd'
                ])->id;
                $grant_type = UserGrantCode::GRANT_TYPE_STRIPE;
            } else {
                $code = generateUniqueCode(6);
                while (UserGrantCode::where('code', $code)->exists()) {
                    $code = generateUniqueCode(6);
                }
                $grant_type = UserGrantCode::GRANT_TYPE_CRYPTO;
            }

            $userGrantCode = UserGrantCode::create([
                'student_id' => $student->id,
                'code' => $code,
                'grant_amount' => UserGrantCode::DEFAULT_GRANT_AMOUNT,
                'grant_type' => $grant_type,
                'expired_at' => date('Y-m-d H:i:s', strtotime('+3 months')),
                'is_active' => true
            ]);

            if ($userGrantCode) {
                $message = 'Student grant approved! Your grant code is : ' . $userGrantCode->code;
                $user = User::find($student->user_id);
                $user->notify(new StudentGrantNotification($userGrantCode, $message, $student->user_id));
            }
        }

        return $student->fresh();
    }
}