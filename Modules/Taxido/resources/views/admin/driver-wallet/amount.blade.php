@use('App\Enums\RoleEnum')
<div class="contentbox">
    <div class="inside">
        <div class="contentbox-title">
            <h3>{{ __('taxido::static.wallets.wallet_balance') }}</h3>
        </div>
        <div class="wallet-detail">
            <div class="wallet-detail-content">
                <div class="wallet-amount">
                    <div class="wallet-icon">
                        <i class="ri-wallet-line"></i>
                    </div>
                    <div>
                        <div class="form-group row">
                            <h4>{{ getDefaultCurrency()?->symbol }}<input class="form-control" type="text"
                                    name="name" min="1" id="balanceLabel" readonly
                                    value="{{ isset($balance) ? number_format($balance, 2) : '0.00' }}"></h4>
                        </div>
                    </div>
                </div>
                @if (Auth::user()->hasRole(RoleEnum::ADMIN))
                    <form action="{{ route('admin.driver-wallet.update.balance') }}" method="POST" id=""
                        class="wallet-form">
                        @csrf
                        <input class="form-control driverId" type="hidden" name="driver_id"
                            value="{{ request()->query('driver_id') }}" placeholder="+ ">
                         <div class="form-group row">
                            <label class="col-md-2 d-none"
                                for="price">{{ __('taxido::static.wallets.amount') }}</label>
                     <input class="form-control" name="type" type="hidden" value="" placeholder="type">
                              <div class="col-md-10">
                                <input class='form-control' id="balanceInput" type="number" name="balance"
                                    class="balance" min="1" value=""
                                    placeholder="{{ __('taxido::static.wallets.credit_debit') }}">
                            </div>
                        </div>
                        <textarea class="form-control" id="noteInput" name="note" rows="1"
                            placeholder="{{ __('taxido::static.wallets.enter_note') }}"></textarea>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="credit btn btn-primary" name="type" value="credit"
                                id="creditBtn" data-bs-toggle="modal" data-bs-target="#confirmation" disabled>
                                {{ __('taxido::static.wallets.credit') }}
                                <i class="ri-inbox-archive-line"></i>
                            </button>
                            <button type="button" class="debit btn btn-secondary" value="debit" name="type"
                                id="debitBtn" data-bs-toggle="modal" data-bs-target="#confirmation" disabled>
                                {{ __('taxido::static.wallets.debit') }}
                                <i class="ri-inbox-unarchive-line"></i>
                            </button>
                        </div>

                        <!-- confirmation modal -->
                        <div class="modal fade confirmation-modal" id="confirmation" tabindex="-1" role="dialog"
                            aria-labelledby="confirmationLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-start confirmation-data">
                                        <div class="main-img">
                                            <div class="delete-icon">
                                                <i class="ri-question-mark"></i>
                                            </div>
                                        </div>
                                        <h4 class="modal-title">{{ __('taxido::static.wallets.confirmation') }}</h4>
                                        <p>
                                            {{ __('taxido::static.wallets.modal') }}
                                        </p>
                                        <div class="d-flex">
                                            <input type="hidden" id="inputType" name="type" value="">
                                            <button type="button" class="btn cancel btn-light me-2" data-bs-dismiss="modal">{{ __('taxido::static.wallets.no') }}</button>
                                            <button type="submit" class="btn btn-primary delete delete-btn spinner-btn">{{ __('taxido::static.wallets.yes') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function($) {
            "use strict";
            document.getElementById('confirmation').addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var type = button.getAttribute('value');
                document.getElementById("inputType").value = type;
            });

            const balanceInput = () => {
                let creditBtn = $("#creditBtn");
                let debitBtn = $("#debitBtn");
                let balanceInput = parseFloat($("#balanceInput").val());
                let balanceLabel = $("#balanceLabel").val();
                balanceLabel = balanceLabel.replace(/[,]/g, '').replace(/[^\d.]/g, '');
                let isConsumerSelected = $("#select-driver").val();
                let disableButtons = (
                    balanceInput <= 0 ||
                    Number.isNaN(balanceInput) ||
                    Number.isNaN(balanceLabel) ||
                    !isConsumerSelected
                );
                creditBtn.prop('disabled', disableButtons || balanceInput <= 0);
                debitBtn.prop('disabled', disableButtons || balanceInput > balanceLabel || balanceLabel <= 0);
            };

            $("#balanceInput").on("input", balanceInput);
            $("#select-driver").on("change", balanceInput);

            $('#addNoteCheck').change(function() {
                $('#noteInput').toggleClass('d-none', !this.checked);
            });
        })(jQuery);
    </script>
@endpush
