@props([
    'columns',
    'data',
    'filters',
    'total',
    'search',
    'actions',
    'bulkactions',
    'viewActionBox' => [],
    'actionButtons' => [],
    'modalActionButtons' => [],
])
<div class="table-main">
    <form method="GET" class="table-form mb-0">
        <div class="table-top-panel">
            @if (isset($filters) || (isset($search) && $search))
                <div class="top-part mb-md-2 mb-0">
                    @isset($filters)
                        <ul class="top-part-left m-0">
                            @foreach ($filters as $filter)
                                @php
                                    $filterActive =
                                        (request()->filled('filter') && request()->filter == $filter['slug']) ||
                                        (!request()->filled('filter') && $loop->first);
                                @endphp
                                <li class="{{ $filter['slug'] }}">
                                    <a href="{{ url()->current() . '?filter=' . $filter['slug'] }}"
                                        @if ($filterActive) class="current" @endif>
                                        {{ $filter['title'] }}
                                        <span class="count">({{ isset($filter['count']) ? $filter['count'] : 0 }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endisset
                    @if (isset($search) && $search)
                        @include('components.table.table-search')
                    @endif
                </div>
            @endif
            @include('components.table.table-action', ['total' => $total, 'bulkactions' => $bulkactions])
        </div>
        <div class="table-responsive custom-scrollbar">
            <table class="table">
                <thead>
                    <tr>
                        @include('components.table.table-header', ['columns' => $columns])
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            @if (!is_string($row))
                                <td class="check-column">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="ids[]"
                                            value="{{ $row['id'] ?? null }}"
                                            data-system-reserved="{{ @$row['system_reserve'] ?? 0 }}">
                                    </div>
                                </td>
                            @endif
    </form>

    @foreach ($columns as $column)
        @include('components.table.table-body', [
            'system_reserved' => !is_string($row) ? $row['system_reserve'] : 0,
            'column' => $column,
            'row' => $row,
            'actionButtons' => $actionButtons,
            'modalActionButtons' => $modalActionButtons,
        ])
    @endforeach
    </tr>
@empty
    <tr class="no-items">
        <td class="colspan" colspan="{{ count($columns) + 1 }}">
            {{ __('No') }} <span>{{ __('Data') }}</span> {{ __('Found') }}
        </td>
    </tr>
    @endforelse
    {{-- <tr>
                        <td class="colspan p-0" colspan="{{ count($columns) + 1 }}">
                            <div class="loader-wrapper">
                                <div class="loader"></div>
                            </div>
                        </td>
                    </tr> --}}
    </tbody>
    <tfoot>
        <tr>
            @include('components.table.table-header', ['columns' => $columns])
        </tr>
    </tfoot>
    </table>
</div>
@if ($data)
    {{ $data?->appends(['paginate' => request()?->paginate])->links() }}
@endif
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            function updateCheckAll() {
                const totalCheckboxes = $('.form-check-input[name="ids[]"]').not(':disabled').length;
                const checkedCheckboxes = $('.form-check-input[name="ids[]"]:checked').not(':disabled').length;
                $('.checkAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            }

            $('.form-check-input[name="ids[]"]').change(updateCheckAll);

            // Check/uncheck all checkboxes when the "All" checkbox is clicked
            $('.checkAll').change(function() {
                const isChecked = $(this).is(':checked');
                $('.form-check-input[name="ids[]"]').not(':disabled').prop('checked', isChecked);
                updateCheckAll();
            });

            $('.form-check-input[name="ids[]"]').each(function() {
                var isReserved = $(this).data('system-reserved');
                if (isReserved) {
                    $(this).prop('disabled', true);
                }
            });
        });

        $(document).ready(function() {
            $(document).on('change', '.toggle-class', function() {
                let status = $(this).prop('checked') ? 1 : 0;
                let url = $(this).data('route');
                let clickedToggle = $(this);
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: {
                        status: status,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(data) {
                        clickedToggle.prop('checked', status);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(error)
                    }
                });
            });
        });
    </script>
@endpush
