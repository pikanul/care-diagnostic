@extends('layouts.admin', ['title' => 'Footer Sections'])

@section('content')
    <div class="panel" style="margin-bottom:16px;">
        <h2>{{ $editingSection ? 'Edit Footer Section' : 'Add Footer Section' }}</h2>
        <form class="form-grid" method="POST" action="{{ $editingSection ? route('admin.footer-sections.update', $editingSection) : route('admin.footer-sections.store') }}">
            @csrf
            @if ($editingSection)
                @method('PUT')
            @endif

            <label>Title (English)
                <input type="text" name="title_en" value="{{ old('title_en', $editingSection->title_en ?? '') }}" required>
            </label>
            <label>Title (Bangla)
                <input type="text" name="title_bn" value="{{ old('title_bn', $editingSection->title_bn ?? '') }}" required>
            </label>
            <label>Display Order
                <input type="number" name="display_order" value="{{ old('display_order', $editingSection->display_order ?? 0) }}" min="0" required>
            </label>
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editingSection->is_active ?? true))> Active</label>
            <button type="submit">{{ $editingSection ? 'Update' : 'Create' }}</button>
        </form>
    </div>

    <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <h2 style="margin:0;">Footer Sections</h2>
            <button type="submit" form="footer-sections-order-form">Save Order</button>
        </div>

        <form id="footer-sections-order-form" method="POST" action="{{ route('admin.footer-sections.reorder') }}">
            @csrf
        </form>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($sections as $section)
                <div class="table-row">
                    <div>
                        <input type="number" name="orders[{{ $section->id }}]" value="{{ $section->display_order }}" min="0" form="footer-sections-order-form">
                    </div>
                    <div>
                        <strong>{{ $section->title_en }}</strong><br>
                        <span class="muted">{{ $section->title_bn }}</span>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a class="button" href="{{ route('admin.footer-sections.edit', $section) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.footer-sections.toggle', $section) }}">
                            @csrf
                            <button type="submit">{{ $section->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                        @if ($section->deleted_at)
                            <form method="POST" action="{{ route('admin.footer-sections.restore', $section->id) }}">
                                @csrf
                                <button type="submit">Restore</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.footer-sections.destroy', $section) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
