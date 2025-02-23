<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ForgotPassword;
use Illuminate\Validation\Rule;
use Modules\Taxido\Models\Rider;
use App\Http\Traits\MessageTrait;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Taxido\Emails\VerifyEmail;
use Illuminate\Support\Facades\Validator;
use Modules\Taxido\Http\Requests\Api\SocialLoginRequest;

class AuthController extends Controller
{
    use MessageTrait;

    public function login_with_email(Request $request)
    {
        try {
            if ($request->has('email') && $request->has('password')) {
                $user = User::where('email', $request->email)?->first();

                if($user) {
                    if(!Hash::check($request->password, $user->password)) {
                        throw new Exception(__('static.auth.invalid_credentials'), 400);
                    }
                    $token = $user->createToken('auth_token')->plainTextToken;
                    $user?->tokens()?->update([
                        'tokenable_type' => $user->getMorphClass(),
                        'role_type' => $user->getRoleNames()->first(),
                    ]);

                    $user?->update([
                        'fcm_token' => $request->fcm_token
                    ]);

                    return [
                        'access_token' => $token,
                        'permissions' => $user->getAllPermissions(),
                        'success' => true,
                        'is_registered' => true,
                    ];
                }

                try {

                    $token = $this->generateToken(email: $request->email);
                    Mail::to($request->email)->queue(new VerifyEmail($token));

                    return [
                        'message' => __('taxido::static.auth.sended_otp_registered_numb'),
                        'success' => true
                    ];

                } catch (Exception $e) {

                    throw new Exception($e->getMessage(), $e->getCode());
                }

            }

            throw new Exception(__('static.auth.invalid_login_credentials'), 400);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function generateToken($country_code = null, $phone = null, $email = null)
    {
        $token = rand(111111, 999999);
        DB::table('auth_tokens')->insert([
            'token' => $token,
            'phone' => '+' . $country_code . $phone,
            'email' =>$email,
            'created_at' => Carbon::now()
        ]);

        return $token;
    }

    public function login_with_numb(Request $request)
    {
        try {

            if (isSMSLoginEnable()) {
                if ($this->verifyLogin($request)) {
                    $token = $this->generateToken($request->country_code, $request->phone);

                    $sendTo = '+' . $request->country_code . $request->phone;
                    $message =  'Your OTP is ' . $token;
                    sendSMS($sendTo, $message);

                    return [
                        'message' => __('taxido::static.auth.sended_otp_registered_numb'),
                        'success' => true
                    ];
                }
            }

            throw new Exception(__('taxido::static.auth.sms_login_not_enable'), 400);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyOtp(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'otp' => 'required',
                'email' => 'exists:users|email',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $verify = DB::table('password_resets')
                ->where('otp', $request->otp)
                ->where('email', $request->email)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$verify) {
                throw new Exception(__('auth.invalid_otp_or_email'), 400);
            }

            $user = User::firstOrCreate(['email' => $verify->email], [
                'email' => $verify->email,
                'code' => $request->code,
                'status' => true,
            ]);

            $user?->update([
                'fcm_token' => $request->fcm_token
            ]);

            if (! $user) {
                throw new Exception(__('auth.user_not_exists'), 404);
            }

            if (! $user->status) {
                throw new Exception(__('auth.user_inactive'), 400);
            }

            return [
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user,
                'success' => true,
            ];

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyRiderToken(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'country_code' => 'required',
                'phone' => 'required',
                'token' => 'required',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $verify_otp = DB::table('auth_tokens')
                ->where('token', $request->token)
                ->where('phone', '+' . $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$verify_otp) {
                throw new Exception(__('taxido::static.auth.invalid_auth_token'), 400);
            }

            $rider = User::where('phone', (string) $request->phone)->first();
            if ($rider) {
                if ($rider?->role?->name != RoleEnum::RIDER) {
                    throw new Exception(__('Only Rider can be allow'), 404);
                }

                if (!$rider && isset($request->phone)) {
                    throw new Exception(__('taxido::static.auth.no_account_linked'), 404);
                }

                if (!$rider->status) {
                    throw new Exception(__('taxido::static.auth.disabled_account'), 403);
                }

                $token = $rider->createToken('auth_token')->plainTextToken;
                $rider->tokens()->update([
                    'role_type' => $rider->getRoleNames()->first(),
                ]);

                $rider?->update([
                    'fcm_token' => $request->fcm_token
                ]);

                return [
                    'access_token' => $token,
                    'permissions' => $rider->getAllPermissions(),
                    'is_registered' => true,
                    'success' => true,
                ];
            }

            return [
                'message' => __('taxido::auth.otp_verified'),
                'is_registered' => false,
                'success' => true,
            ];
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function socialLogin(SocialLoginRequest $request)
    {
        $loginMethod = $request->input('login_type');
        $rider = (object) $request->input('rider');

        DB::beginTransaction();
        try {
            $rider = $this->createOrGetrider($loginMethod, $rider);
            if ($request->fcm_token) {
                $rider->fcm_token = $request->fcm_token;
                $rider->save();
            }

            DB::commit();

            if ($rider->status) {
                return response()->json([
                    'success' => true,
                    'access_token' => $rider->createToken('sanctum')->plainTextToken,
                ], 200);
            }

            throw new Exception(__('auth.user_deactivated'), 403);

        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    private function createOrGetUser($loginMethod, $user)
    {
        if ($loginMethod === 'phone') {
            $phone = $user->phone;
            $code = $user->code;

            $existingUser = Rider::where('phone', $phone)->first();

            if ($existingUser) {
                return $existingUser;
            }

            $newUser = Rider::create([
                'status' => true,
                'phone' => $phone,
                'code' => $code,
            ]);
        } else {
            $email = $user->email;
            $name = $user->name;

            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                return $existingUser;
            }

            $newUser = User::create([
                'status' => true,
                'email' => $email ?? null,
                'name' => $name ?? null,
            ]);
        }

        $userRole = Role::where('name', RoleEnum::RIDER)->first();
        if ($userRole) {
            $newUser->assignRole($userRole);
        }

        return $newUser;
    }
    public function verifyLogin(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email'    => ['nullable', Rule::requiredIf(!$request->phone), 'email'],
                'password' => ['nullable', Rule::requiredIf(!$request->phone)],
                'phone' => ['nullable', Rule::requiredIf(!$request->email)],
                'country_code' => ['nullable', Rule::requiredIf(!$request->email)]
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            if (isset($request->email)) {
                $rider = Rider::where('email', $request->email)->orWhere('phone', (string) $request->phone)->first();
                if (!$rider && isset($request->email)) {
                    throw new Exception(__('static.auth.no_linked_email'), 400);
                }

                if (!$rider->status) {
                    throw new Exception(_('static.auth.disabled_account'), 400);
                }
                return $rider;
            }

            return true;
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forgotPassword(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email,deleted_at,NULL|string',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $token = rand(111111, 999909);
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            Mail::to($request->email)->send(new ForgotPassword($token));
            return [
                'message' => __('auth.email_verification_sent'),
                'success' => true
            ];
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function riderRegister(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'nullable|string|min:8|confirmed',
                'password_confirmation' => 'nullable',
                'country_code' => 'required',
                'phone' => 'required|min:9|unique:users,phone,NULL,id,deleted_at,NULL',
                'referral_code' => 'nullable|string|exists:users,referral_code,deleted_at,NULL',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $rider = Rider::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => (string) $request->phone,
                'fcm_token' => $request->fcm_token,
                'referral_code' => getReferralCodeByName($request->name)
            ]);

            $rider->assignRole(RoleEnum::RIDER);
            if (riderWalletIsEnable()) {
                $rider->wallet()->create();
                $rider->wallet;
            }

            if ($request->address) {
                $rider->addresses()->create([
                    'title' => $request->address['title'] ?? null,
                    'address' => $request->address['address'] ?? null,
                    'street_address' => $request->address['street_address'] ?? null,
                    'area_locality' => $request->address['area_locality'] ?? null,
                    'city' => $request->address['city'] ?? null,
                    'country_code' => $request->address['country_code'] ?? null,
                    'phone' => (string) $request->address['phone'] ?? null,
                    'postal_code' => $request->address['postal_code'] ?? null,
                    'country_id' => $request->address['country_id'] ?? null,
                    'state_id' => $request->address['state_id'] ?? null,
                    'latitude' => $request->address['latitude'] ?? null,
                    'longitude' => $request->address['longitude'] ?? null,
                ]);
            }

            DB::commit();
            if ($request->referral_code) {
                $referred_by_id = $this->getReferredRiderId($request->referral_code);
                if ($referred_by_id) {
                    $rider->referred_by_id = $referred_by_id;
                    $rider->save();
                }
            }

            $token = $rider->createToken('auth_token')->plainTextToken;
            $rider->tokens()->update([
                'role_type' => $rider->getRoleNames()->first(),
            ]);

            return [
                'access_token' => $token,
                'permissions' => $rider->getAllPermissions(),
                'success' => true,
            ];
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email|max:255|exists:users,email,deleted_at,NULL|strings',
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $user = DB::table('password_resets')
                ->where('token', $request->token)
                ->where('email', $request->email)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$user) {
                throw new Exception(__('auth.invalid_email_token'), 400);
            }

            User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where('email', $request->email)->delete();
            DB::commit();

            return [
                'message' => __('auth.password_changed'),
                'success' => true
            ];
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getReferredRiderId($referral_code)
    {
        return Rider::where('referral_code', $referral_code)?->whereNull('deleted_at')?->pluck('id');
    }
}
