@extends('layouts.admin', ['title' => 'Footer Links'])

@section('content')
    <div class="panel" style="margin-bottom:16px;">
        <h2>{{ $editingLink ? 'Edit Footer Link' : 'Add Footer Link' }}</h2>
        <form class="form-grid" method="POST" action="{{ $editingLink ? route('admin.footer-links.update', $editingLink) : route('admin.footer-links.store') }}">
            @csrf
            @if ($editingLink)
                @method('PUT')
            @endif

            <label>Footer Section
                <select name="footer_section_id" required>
                    <option value="">Select section</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected((string) old('footer_section_id', $editingLink->footer_section_id ?? '') === (string) $section->id)>{{ $section->title_en }}</option>
                    @endforeach
                </select>
            </label>
            <label>Label (English)
                <input type="text" name="label_en" value="{{ old('label_en', $editingLink->label_en ?? '') }}" required>
            </label>
            <label>Label (Bangla)
                <input type="text" name="label_bn" value="{{ old('label_bn', $editingLink->label_bn ?? '') }}" required>
            </label>
            <label>URL
                <input type="text" name="url" value="{{ old('url', $editingLink->url ?? '') }}" required>
            </label>
            <label>Display Order
                <input type="number" name="display_order" value="{{ old('display_order', $editingLink->display_order ?? 0) }}" min="0" required>
            </label>
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingLink->is_active ?? true))> Active</label>
            <label class="check"><input type="checkbox" name="open_in_new_tab" value="1" @checked(old('open_in_new_tab', $editingLink->open_in_new_tab ?? false))> Open in new tab</label>
            <button type="submit">{{ $editingLink ? 'Update' : 'Create' }}</button>
        </form>
    </div>

    <div class="panel">
        <div class="admin-list-head">
            <div>
                <h2>Footer Links</h2>
                <p class="muted" style="margin:4px 0 0;">Edit, activate, delete, and reorder footer links.</p>
            </div>
            <button type="submit" form="footer-links-order-form">Save Order</button>
        </div>

        <form id="footer-links-order-form" method="POST" action="{{ route('admin.footer-links.reorder') }}">
            @csrf
        </form>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($links as $link)
                <div class="table-row admin-table-row">
                    <div>
                        <input class="admin-order-input" type="number" name="orders[{{ $link->id }}]" value="{{ $link->display_order }}" min="0" form="footer-links-order-form" aria-label="Display order for {{ $link->label_en }}">
                    </div>
                    <div>
                        <strong>{{ $link->label_en }}</strong><br>
                        <span class="muted">{{ $link->label_bn }}</span><br>
                        <span class="muted">{{ $link->section?->title_en }}</span>
                    </div>
                    <div class="admin-row-actions">
                        <a class="button" href="{{ route('admin.footer-links.edit', $link) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.footer-links.toggle', $link) }}">
                            @csrf
                            <button class="secondary" type="submit">{{ $link->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        @if ($link->deleted_at)
                            <form method="POST" action="{{ route('admin.footer-links.restore', $link->id) }}">
                                @csrf
                                <button class="secondary" type="submit">Restore</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.footer-links.destroy', $link) }}">
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
