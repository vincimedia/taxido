<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\PaymentAccount;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class PaymentAccountRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'bank_name' => 'like',
    ];

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    function model()
    {
        return PaymentAccount::class;
    }

    public function show($id)
    {
        try {

            return $this->model->findOrFail($id);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $paymentAccount =  $this->model->create([
                'user_id' => getCurrentUserId(),
                'bank_name' => $request->bank_name,
                'bank_holder_name' => $request->bank_holder_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc' => $request->ifsc,
                'swift' => $request->swift,
            ]);

            DB::commit();
            return $paymentAccount;
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $paymentAccount = $this->model->findOrFail($id);
            $paymentAccount->update($request);

            DB::commit();
            return $paymentAccount;
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

            return $this->model->findOrFail($id)->destroy($id);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
