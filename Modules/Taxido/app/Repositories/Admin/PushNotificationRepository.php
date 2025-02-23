<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use App\Models\User;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\PushNotification;
use Prettus\Repository\Eloquent\BaseRepository;

class PushNotificationRepository extends BaseRepository
{
    protected $ride;
    protected $user;

    function model()
    {
        $this->user = new User();
        return PushNotification::class;
    }

    public function index($pushNotificationTable)
    {
        return view('taxido::admin.push-notification.index', ['tableConfig' => $pushNotificationTable]);
    }

    public function create($request)
    {
        return view('taxido::admin.push-notification.create');
    }

    public function sendNotification($request)
    {
        $pushNotification = $this->model->create([
            'send_to' => $request->send_to,
            'ride_id' => $request->ride_id ?? null,
            'title' => $request->title,
            'message' => $request->description,
            'url' => $request->url,
            'notification_type' => $request->send_to,
            'user_id' => getCurrentUserId(),
        ]);

        if ($request->zones) {
            $pushNotification->zones()->attach($request->zones);
            $pushNotification->zones;
        }
        if ($request->file('image')) {
            $media = $pushNotification->addMediaFromRequest('image')->toMediaCollection('notification_image');
            $fullImageUrl = $media->getFullUrl();
            $pushNotification->image_url = $fullImageUrl;
            $pushNotification->save();
        }
        $users = [];

        if ($request->send_to === 'all_riders') {
            $users = User::role(RoleEnum::RIDER)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
        } elseif ($request->send_to === 'all_drivers') {
            $users = User::role(RoleEnum::DRIVER)->whereNotNull('fcm_token')->pluck('fcm_token')->all();
        }

        foreach ($users as $token) {
            $notification = [
                'message' => [
                    'data' => [
                        'url' => $request->url ?? null,
                    ],
                    'notification' => [
                        'title' => $request->title,
                        'body' => $request->description,
                        'image' => $pushNotification->image_url ?? null,
                    ],
                    'token' => $token,
                ],
            ];

            pushNotification($notification);
        }

        return redirect()->route('admin.push-notification.index')->with('success', __('taxido::static.push_notification.sent_notification'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $pushNotification = $this->model->findOrFail($id);
            $pushNotification->destroy($id);

            DB::commit();
            return to_route('admin.push-notification.index')->with('success', __('taxido::static.push_notification.delete_successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $pushNotification = $this->model->onlyTrashed()->findOrFail($id);
            $pushNotification->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.push_notification.permanent_delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}