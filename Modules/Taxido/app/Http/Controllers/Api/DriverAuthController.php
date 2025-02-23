<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ForgotPassword;
use App\Http\Traits\MessageTrait;
use Modules\Taxido\Models\Driver;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Models\Document;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DriverAuthController extends Controller
{
    use MessageTrait;

    public function self()
    {
        try {

            $driver_id = getCurrentUserId();
            $driver = Driver::without(['zones', 'reviews'])?->findOrFail($driver_id);
            return $driver->setAppends([
                'role',
                'rating_count',
                'total_driver_commission',
                'total_pending_rides',
                'total_complete_rides',
                'total_cancel_rides',
                'total_active_rides',
                'total_driver_commission',
                'pending_withdraw_requests_count'
            ]);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function driverLogin(Request $request)
    {
        try {

            if (isSMSLoginEnable()) {
                if ($this->verifyLogin($request)) {
                    $token = rand(111111, 999999);
                    DB::table('auth_tokens')->insert([
                        'token' => $token,
                        'phone' => $request->country_code . $request->phone,
                        'created_at' => Carbon::now(),
                    ]);

                    $sendTo = '+' . $request->country_code . $request->phone;
                    $message =  'Your OTP is ' . $token;
                    sendSMS($sendTo, $message);

                    return [
                        'message' => __('taxido::static.auth.sended_otp_registered_numb'),
                        'success' => true,
                    ];
                }
            }

            throw new Exception(__('taxido::static.auth.sms_login_not_enable'), 400);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyDriverToken(Request $request)
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
                ->where('phone', $request->country_code . $request->phone)
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$verify_otp) {
                throw new Exception(__('taxido::static.auth.invalid_auth_token'), 400);
            }

            $driver = Driver::where('phone', (string) $request->phone)->first();
            if ($driver) {
                if ($driver->role?->name != RoleEnum::DRIVER) {
                    throw new Exception(__('Only Driver can be allow'), 404);
                }

                if (!$driver && isset($request->phone)) {
                    throw new Exception(__('taxido::static.auth.no_account_linked'), 404);
                }

                if (!$driver->status) {
                    throw new Exception(__('taxido::static.auth.disabled_account'), 403);
                }

                $token = $driver->createToken('auth_token')->plainTextToken;
                $driver->tokens()->update([
                    'role_type' => $driver->getRoleNames()->first(),
                ]);

                $driver?->update([
                    'fcm_token' => $request?->fcm_token,
                ]);

                return [
                    'access_token' => $token,
                    'permissions' => $driver->getAllPermissions(),
                    'is_registered' => true,
                    'success' => true,
                    'is_registered' => true,
                ];
            }

            return [
                'message' => 'OTP Verified Successfully!',
                'is_registered' => false,
                'success' => true,
            ];

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyLogin(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'phone' => ['nullable', 'required'],
                'country_code' => ['nullable', 'required'],
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
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

            $token = rand(111111, 999999);
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

    public function driverRegister(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                'country_code' => 'required',
                'phone' => 'required|min:9|unique:users,phone,NULL,id,deleted_at,NULL',
                'referral_code' => 'nullable|string|exists:users,referral_code,deleted_at,NULL',
                'address.country_id' => 'nullable|exists:countries,id',
                'address.state_id' => 'nullable|exists:states,id',
                'documents.*.slug' => 'exists:documents,slug,deleted_at,NULL',
                'service_id' => 'required|exists:services,id,deleted_at,NULL',
                'service_category_id' => 'required|exists:service_categories,id,deleted_at,NULL',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->messages()->first(), 422);
            }

            $driver = Driver::create([
                'username' => strtolower($request->username),
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => (string) $request->phone,
                'can_accept_ride' => $request->can_accept_ride,
                'can_accept_parcel' => $request->can_accept_parcel,
                'service_id' => $request->service_id,
                'fcm_token' => $request?->fcm_token,
                'service_category_id' => $request->service_category_id,
            ]);

            $driver->assignRole(RoleEnum::DRIVER);
            if (driverWalletIsEnable()) {
                $driver->wallet()->create();
                $driver->wallet;
            }

            if (!empty($request->address)) {
                $driver->addresses()->create($request->address);
            }

            if (!empty($request->vehicle)) {
                $driver->vehicle_info()->create($request->vehicle);
            }

            if (!empty($request->payment_account)) {
                $driver->payment_account()->create($request->payment_account);
            }

            if (!empty($request->documents) && is_array($request->documents)) {
                if(count($request->documents)) {
                    foreach ($request->documents as $document) {
                        if (is_array($document)) {
                            $attachment_id = addMedia(createAttachment(), $document['file'])?->id;
                            $document_id = Document::where('slug', $document['slug'])->value('id');
                            $driver->documents()->create([
                                'document_id' => $document_id,
                                'document_image_id' => $attachment_id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            if ($request->referral_code) {
                $referred_by_id = $this->getReferredDriverId($request->referral_code);
                if ($referred_by_id) {
                    $driver->referred_by_id = $referred_by_id;
                    $driver->save();
                }
            }

            $token = $driver->createToken('auth_token')->plainTextToken;
            $driver->tokens()->update([
                'role_type' => $driver->getRoleNames()->first(),
            ]);

            return [
                'access_token' => $token,
                'permissions' => $driver->getAllPermissions(),
                'success' => true,
                'is_registered' => true,
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

    public function getReferredDriverId($referral_code)
    {
        return Driver::Where('referral_code', $referral_code)?->whereNull('deleted_at')?->pluck('id');
    }
}
