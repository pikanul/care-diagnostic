@extends('layouts.admin', ['title' => 'Navigation'])

@section('content')
    <div class="panel" style="margin-bottom:16px;">
        <h2>{{ $editingItem ? 'Edit Navigation Item' : 'Add Navigation Item' }}</h2>
        <form class="form-grid" method="POST" action="{{ $editingItem ? route('admin.navigation.update', $editingItem) : route('admin.navigation.store') }}">
            @csrf
            @if ($editingItem)
                @method('PUT')
            @endif

            <label>Menu Label (English)
                <input type="text" name="label_en" value="{{ old('label_en', $editingItem->label_en ?? '') }}" required>
            </label>
            <label>Menu Label (Bangla)
                <input type="text" name="label_bn" value="{{ old('label_bn', $editingItem->label_bn ?? '') }}" required>
            </label>
            <label>URL
                <input type="text" name="url" value="{{ old('url', $editingItem->url ?? '') }}" required>
            </label>
            <label>Parent Menu
                <select name="parent_id">
                    <option value="">None</option>
                    @foreach ($parentOptions as $parent)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $editingItem->parent_id ?? '') === (string) $parent->id)>{{ $parent->label_en }}</option>
                    @endforeach
                </select>
            </label>
            <label>Display Order
                <input type="number" name="display_order" value="{{ old('display_order', $editingItem->display_order ?? 0) }}" min="0" required>
            </label>
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingItem->is_active ?? true))> Active</label>
            <label class="check"><input type="checkbox" name="open_in_new_tab" value="1" @checked(old('open_in_new_tab', $editingItem->open_in_new_tab ?? false))> Open in new tab</label>

            <button type="submit">{{ $editingItem ? 'Update' : 'Create' }}</button>
        </form>
    </div>

    <div class="panel">
        <div class="admin-list-head">
            <div>
                <h2>Navigation Items</h2>
                <p class="muted" style="margin:4px 0 0;">Edit, activate, delete, and reorder menu links.</p>
            </div>
            <button type="submit" form="navigation-order-form">Save Order</button>
        </div>

        <form id="navigation-order-form" method="POST" action="{{ route('admin.navigation.reorder') }}">
            @csrf
        </form>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($items as $item)
                <div class="table-row admin-table-row">
                    <div>
                        <input class="admin-order-input" type="number" name="orders[{{ $item->id }}]" value="{{ $item->display_order }}" min="0" form="navigation-order-form" aria-label="Display order for {{ $item->label_en }}">
                    </div>
                    <div>
                        <strong>{{ $item->label_en }}</strong><br>
                        <span class="muted">{{ $item->label_bn }}</span><br>
                        <span class="muted">{{ $item->url }}</span>
                    </div>
                    <div class="admin-row-actions">
                        <a class="button" href="{{ route('admin.navigation.edit', $item) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.navigation.toggle', $item) }}">
                            @csrf
                            <button class="secondary" type="submit">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        @if ($item->deleted_at)
                            <form method="POST" action="{{ route('admin.navigation.restore', $item->id) }}">
                                @csrf
                                <button class="secondary" type="submit">Restore</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.navigation.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="danger-button" type="submit">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
