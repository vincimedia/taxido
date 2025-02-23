@extends('admin.layouts.master')
@section('title', __('taxido::static.withdraw_requests.title'))
@section('content')
@includeIf('inc.modal',['export' => true,'routes' => 'admin.withdraw-request.export'])
    <div class="row g-4 wallet-main mb-4">
        @if (Auth::user()->hasRole('driver'))
        <div class="col-xxl-8 col-xl-7">
            @includeIf('taxido::admin.withdraw-request.amount')
        </div>
        <div class="col-xxl-4 col-xl-5">
            @includeIf('taxido::admin.withdraw-request.drivers')
        </div>
        @endif
    </div>
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title justify-unset">
                <h3>{{__('taxido::static.withdraw_requests.title')}}</h3>

                @can('withdraw_request.index') 
                    <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="ri-download-line"></i>{{ __('static.export.export') }}
                    </button>
                @endcan
            </div>
            <div class="withdrawRequest-table">
                <x-table
                    :columns="$tableConfig['columns']"
                    :data="$tableConfig['data']"
                    :filters="[]"
                    :actions="[]" 
                    :total="''"
                    :bulkactions="$tableConfig['bulkactions']" 
                    :viewActionBox="$tableConfig['viewActionBox']"
                    :search="true">
                </x-table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
  // Approve withdraw request
$('.request-approved-submit').click(function(e) {
    e.preventDefault();

    var withdrawRequestStatus = $(this).data('status');
    var url = $(this).data('route');
    var requestId = $(this).data('id');

    // Show loader and hide button text
    $('#approved-loader-' + requestId).show();
    $('.btn-text').hide();

    var currentStatus = $('#withdraw-request-status-' + requestId).val();
    if (currentStatus === 'approved' || currentStatus === 'rejected') {
        alert('Status already updated!');
        $('#approved-loader-' + requestId).hide();
        $('.btn-text').show();
        return;
    }

    $.ajax({
        type: "GET",
        url: url,
        data: {
            status: withdrawRequestStatus,
        },
        success: function(data) {
            $('#approved-loader-' + requestId).hide();
            $('.btn-text').show();
            $('#withdraw-request-status-' + requestId).val('approved');
            var row = $('#withdraw-request-row-' + requestId); 
            row.find('.status-column').text('Approved');
            $('#withdraw-request-modal-' + requestId).find('.request-approved-submit, .request-rejected-submit').prop('disabled', true);
            location.reload(); 
        },
        error: function(xhr, status, error) {
            console.error(error);
            $('#approved-loader-' + requestId).hide();
            $('.btn-text').show();
        }
    });
});

// Reject withdraw request
$('.request-rejected-submit').click(function(e) {
    e.preventDefault();

    var withdrawRequestStatus = $(this).data('status');
    var url = $(this).data('route');
    var requestId = $(this).data('id');

    $('#rejected-loader-' + requestId).show();
    $('.btn-text').hide();

    var currentStatus = $('#withdraw-request-status-' + requestId).val();
    if (currentStatus === 'approved' || currentStatus === 'rejected') {
        alert('Status already updated!');
        $('#rejected-loader-' + requestId).hide();
        $('.btn-text').show();
        return;
    }

    $.ajax({
        type: "GET",
        url: url,
        data: {
            status: withdrawRequestStatus,
        },
        success: function(data) {
            $('#rejected-loader-' + requestId).hide();
            $('.btn-text').show();
            $('#withdraw-request-status-' + requestId).val('rejected');
            var row = $('#withdraw-request-row-' + requestId); 
            row.find('.status-column').text('Rejected');
            $('#withdraw-request-modal-' + requestId).find('.request-approved-submit, .request-rejected-submit').prop('disabled', true);
            location.reload(); 
        },
        error: function(xhr, status, error) {
            console.error(error);
            $('#rejected-loader-' + requestId).hide();
            $('.btn-text').show();
        }
    });
});
</script>
@endpush

