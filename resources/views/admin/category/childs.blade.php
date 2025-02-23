

<ol class="dd-list">
    @foreach ($childs as $child)
        <li class="dd-item dd3-item" {{ isset($cat) && $cat->id == $child->id ? 'active' : '' }}
            {{ !$child->status ? 'disabled' : '' }} data-id="{{ $child->id }}">
            <div class="dd-handle dd3-handle">{{ __('static.categories.drag') }}</div>
            <div class="dd3-content">
                {{ $child->name }}
                <form method="POST" action="{{ route('admin.category.destroy', $child->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete" data-bs-toggle="modal" data-bs-target="#confirmation">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    <a href="{{ route('admin.category.edit', [$child->id]) }}" class="edit"><i
                            class="ri-edit-2-line"></i></a>
                </form>
            </div>
        </li>
        @if (count($child->childs))
            @include('admin.category.childs', ['childs' => $child->childs])
        @endif
    @endforeach
</ol>


