<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSection;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterSectionController extends Controller
{
    public function index(): View
    {
        return view('admin.footer-sections.index', [
            'sections' => FooterSection::withTrashed()->orderBy('display_order')->get(),
            'editingSection' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSection($request);
        $section = FooterSection::create($validated);
        AuditLogger::record('footer_section_created', $section, request: $request);

        return back()->with('status', 'Footer section created.');
    }

    public function edit(FooterSection $footerSection): View
    {
        return view('admin.footer-sections.index', [
            'sections' => FooterSection::withTrashed()->orderBy('display_order')->get(),
            'editingSection' => $footerSection,
        ]);
    }

    public function update(Request $request, FooterSection $footerSection): RedirectResponse
    {
        $validated = $this->validateSection($request);
        $oldValues = $footerSection->toArray();
        $footerSection->update($validated);
        AuditLogger::record('footer_section_updated', $footerSection, $oldValues, $footerSection->toArray(), request: $request);

        return redirect()->route('admin.footer-sections.index')->with('status', 'Footer section updated.');
    }

    public function destroy(Request $request, FooterSection $footerSection): RedirectResponse
    {
        $footerSection->delete();
        AuditLogger::record('footer_section_deleted', $footerSection, request: $request);

        return back()->with('status', 'Footer section deleted.');
    }

    public function restore(Request $request, int $footerSection): RedirectResponse
    {
        $section = FooterSection::withTrashed()->findOrFail($footerSection);
        $section->restore();
        AuditLogger::record('footer_section_restored', $section, request: $request);

        return back()->with('status', 'Footer section restored.');
    }

    public function toggle(Request $request, FooterSection $footerSection): RedirectResponse
    {
        $footerSection->update(['is_active' => ! $footerSection->is_active]);
        AuditLogger::record('footer_section_status_changed', $footerSection, request: $request);

        return back()->with('status', 'Footer section status updated.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($validated['orders'] as $id => $order) {
            FooterSection::whereKey($id)->update(['display_order' => $order]);
        }

        AuditLogger::record('footer_sections_reordered', metadata: ['orders' => $validated['orders']], request: $request);

        return back()->with('status', 'Footer section order saved.');
    }

    private function validateSection(Request $request): array
    {
        return $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_bn' => ['required', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
