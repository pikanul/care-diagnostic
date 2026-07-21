@extends('layouts.admin', ['title' => 'Homepage Builder'])

@section('content')
    @php
        $quickSections = [
            'hero-slider' => 'Hero Image',
            'specialist-doctors' => 'Specialist Doctors',
            'diagnostic-test-categories' => 'Accurate Tests, Reliable Results',
            'main-services' => 'Our Services',
        ];
    @endphp

    <div class="panel">
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0;">Homepage Sections</h2>
                <p class="muted">Manage visibility, order, content, media, and preview state for each section.</p>
            </div>
            <button type="submit" form="homepage-order-form">Save Order</button>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;margin-top:16px;">
            @foreach ($quickSections as $sectionKey => $label)
                @php
                    $quickSection = $sections->firstWhere('section_key', $sectionKey);
                @endphp
                @if ($quickSection)
                    <a class="button" href="{{ route('admin.homepage.edit', $quickSection) }}" style="justify-content:space-between;">
                        {{ $label }}
                        <span aria-hidden="true">Edit</span>
                    </a>
                @endif
            @endforeach
        </div>

        <form id="homepage-order-form" method="POST" action="{{ route('admin.homepage.reorder') }}">
            @csrf
        </form>

        <div class="table-list" style="margin-top:16px;">
            @foreach ($sections as $section)
                <div class="table-row">
                    <div>
                        <input type="number" name="orders[{{ $section->id }}]" value="{{ $section->display_order }}" min="0" form="homepage-order-form">
                    </div>
                    <div>
                        <strong>{{ $section->section_label_en }}</strong><br>
                        <span class="muted">{{ $section->section_label_bn }}</span><br>
                        <span class="muted">{{ $section->section_key }} · {{ $section->section_type }}</span>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a class="button" href="{{ route('admin.homepage.edit', $section) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.homepage.toggle', $section) }}">
                            @csrf
                            <button type="submit">{{ $section->is_active ? 'Disable' : 'Enable' }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
