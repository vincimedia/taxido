<?php

namespace Modules\Taxido\Http\Traits;

use Exception;
use App\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RiderWallet;
use Modules\Taxido\Models\DriverWallet;
use Modules\Taxido\Enums\RoleEnum as EnumsRoleEnum;

trait WalletPointsTrait
{
  use WalletHistoryTrait;
  // Wallet
  public function getDriverWallet($driver_id)
  {
    if (driverWalletIsEnable()) {
      $roleName = getRoleNameByUserId($driver_id);
      if ($roleName == EnumsRoleEnum::DRIVER) {
        return DriverWallet::firstOrCreate(['driver_id' => $driver_id]);
      }

      throw new ExceptionHandler("user must be " . EnumsRoleEnum::DRIVER, 400);
    }

    throw new ExceptionHandler(__('taxido::static.wallet_feature_on'), 405);
  }

  public function getRiderWallet($rider_id)
  {
    if (riderWalletIsEnable()) {
      $roleName = getRolesNameByUserId($rider_id);
      if ($roleName == EnumsRoleEnum::RIDER) {
        return RiderWallet::firstOrCreate(['rider_id' => $rider_id]);
      }

      throw new ExceptionHandler("user must be " . EnumsRoleEnum::RIDER, 400);
    }

    throw new ExceptionHandler(__('taxido::static.wallet_feature_on'), 405);
  }


  public function verifyDriverWallet($driver_id, $balance)
  {
    if (isUserLogin()) {
      if ($balance > 0.00) {
        $roleName = getCurrentRoleName();
        if ($roleName != RoleEnum::USER) {
          if (driverWalletIsEnable()) {
            $driverWalletBalance = $this->getDriverWalletBalance($driver_id);
            if ($driverWalletBalance >= $balance) {
              return true;
            }
            throw new Exception(__('taxido::static.wallets.wallet_balance_not_sufficient'), 400);
          }
          throw new Exception(__('taxido::static.wallets.wallet_balance_ride'), 400);
        }
        throw new Exception(__('taxido::static.wallets.wallet_balance_unable'), 400);
      }
    }
    return false;
  }

  public function verifyRiderWallet($rider_id, $balance)
  {
    if (isUserLogin()) {
      if ($balance > 0.00) {
        $roleName = getCurrentRoleName();
        if ($roleName == EnumsRoleEnum::RIDER) {
          if (riderWalletIsEnable()) {
            $riderWalletBalance = $this->getRiderWalletBalance($rider_id);
            if ($riderWalletBalance >= $balance) {
              return true;
            }
            throw new Exception(__('taxido::static.wallets.wallet_balance_not_sufficient'), 400);
          }

          throw new Exception(__('Wallet Disabled'), 400);
        }
        throw new Exception(__('taxido::static.wallets.wallet_balance_ride'), 400);
      }
      throw new Exception(__('taxido::static.wallets.wallet_balance_not_sufficient'), 400);
    }
    return false;
  }

  public function getDriverWalletBalance($driver_id)
  {
    return $this->getDriverWallet($driver_id)->balance;
  }

  public function getRiderWalletBalance($rider_id)
  {
    return $this->getRiderWallet($rider_id)->balance;
  }

  public function creditDriverWallet($driver_id, $balance, $detail)
  {
    $driverWallet = $this->getDriverWallet($driver_id);
    if ($driverWallet) {
      $driverWallet->increment('balance', $balance);
    }

    $this->creditTransaction($driverWallet, $balance, $detail);
    return $driverWallet;
  }

  public function creditRiderWallet($rider_id, $balance, $detail)
  {
    $riderWallet = $this->getRiderWallet($rider_id);
    if ($riderWallet) {
      $riderWallet->increment('balance', $balance);
    }

    $this->creditTransaction($riderWallet, $balance, $detail);
    return $riderWallet;
  }

  public function debitDriverWallet($driver_id, $balance, $detail)
  {
    $driverWallet = $this->getDriverWallet($driver_id);
    if ($driverWallet) {
      if ($driverWallet->balance >= $balance) {
        $driverWallet->decrement('balance', $balance);
        $this->debitTransaction($driverWallet, $balance, $detail);

        return $driverWallet;
      }

      throw new ExceptionHandler(__('taxido::static.wallets.wallet_balance_not_sufficient'), 400);
    }
  }

  public function debitRiderWallet($rider_id, $balance, $detail)
  {
    $riderWallet = $this->getRiderWallet($rider_id);
    if ($riderWallet) {
      if ($riderWallet->balance >= $balance) {
        $riderWallet->decrement('balance', $balance);
        $this->debitTransaction($riderWallet, $balance, $detail);

        return $riderWallet;
      }

      throw new ExceptionHandler(__('taxido::static.wallets.wallet_balance_not_sufficient'), 400);
    }
  }
}
