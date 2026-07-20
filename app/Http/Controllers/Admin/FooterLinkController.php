<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\FooterSection;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FooterLinkController extends Controller
{
    public function index(): View
    {
        return view('admin.footer-links.index', [
            'links' => FooterLink::withTrashed()->with('section')->orderBy('display_order')->get(),
            'sections' => FooterSection::query()->orderBy('display_order')->get(),
            'editingLink' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLink($request);
        $link = FooterLink::create($validated);
        AuditLogger::record('footer_link_created', $link, request: $request);

        return back()->with('status', 'Footer link created.');
    }

    public function edit(FooterLink $footerLink): View
    {
        return view('admin.footer-links.index', [
            'links' => FooterLink::withTrashed()->with('section')->orderBy('display_order')->get(),
            'sections' => FooterSection::query()->orderBy('display_order')->get(),
            'editingLink' => $footerLink,
        ]);
    }

    public function update(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $validated = $this->validateLink($request);
        $oldValues = $footerLink->toArray();
        $footerLink->update($validated);
        AuditLogger::record('footer_link_updated', $footerLink, $oldValues, $footerLink->toArray(), request: $request);

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link updated.');
    }

    public function destroy(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->delete();
        AuditLogger::record('footer_link_deleted', $footerLink, request: $request);

        return back()->with('status', 'Footer link deleted.');
    }

    public function restore(Request $request, int $footerLink): RedirectResponse
    {
        $link = FooterLink::withTrashed()->findOrFail($footerLink);
        $link->restore();
        AuditLogger::record('footer_link_restored', $link, request: $request);

        return back()->with('status', 'Footer link restored.');
    }

    public function toggle(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->update(['is_active' => ! $footerLink->is_active]);
        AuditLogger::record('footer_link_status_changed', $footerLink, request: $request);

        return back()->with('status', 'Footer link status updated.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($validated['orders'] as $id => $order) {
            FooterLink::whereKey($id)->update(['display_order' => $order]);
        }

        AuditLogger::record('footer_links_reordered', metadata: ['orders' => $validated['orders']], request: $request);

        return back()->with('status', 'Footer link order saved.');
    }

    private function validateLink(Request $request): array
    {
        return $request->validate([
            'footer_section_id' => ['required', Rule::exists('footer_sections', 'id')],
            'label_en' => ['required', 'string', 'max:255'],
            'label_bn' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'open_in_new_tab' => ['nullable', 'boolean'],
        ]);
    }
}
