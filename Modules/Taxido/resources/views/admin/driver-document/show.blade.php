@isset($document)
<div class="modal-icon-box">
  <a href="javascript:void(0)" data-bs-toggle="modal" class="dark-icon-box" data-bs-target="#documentModal{{$document?->id}}">
    <i class="ri-eye-line"></i>
  </a>
  <!-- Modal -->
  <div class="modal fade document-view-modal" id="documentModal{{$document?->id}}" tabindex="-1" aria-labelledby="documentModalLabel{{$document?->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-0">
          <div class="doc-detail">
            <div class="doc-img">
              <img src="{{ $document?->document_image?->original_url ?? asset('images/avtar/16.jpg') }}" alt="user">
            </div>
            @isset($document?->document_no)
            <h5>{{ __('taxido::static.driver_documents.document_id') }}: <span><strong>{{$document?->document_no}}</strong></span></h5>
            @endisset
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endisset
