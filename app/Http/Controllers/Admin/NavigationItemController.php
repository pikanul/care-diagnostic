<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NavigationItemController extends Controller
{
    public function index(): View
    {
        return view('admin.navigation.index', [
            'items' => NavigationItem::withTrashed()
                ->where('location', 'header')
                ->orderBy('display_order')
                ->get(),
            'parentOptions' => NavigationItem::query()
                ->where('location', 'header')
                ->whereNull('parent_id')
                ->orderBy('display_order')
                ->get(),
            'editingItem' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateItem($request);
        $validated['location'] = 'header';

        $item = NavigationItem::create($validated);
        AuditLogger::record('navigation_item_created', $item, request: $request);

        return back()->with('status', 'Navigation item created.');
    }

    public function edit(NavigationItem $navigationItem): View
    {
        return view('admin.navigation.index', [
            'items' => NavigationItem::withTrashed()
                ->where('location', 'header')
                ->orderBy('display_order')
                ->get(),
            'parentOptions' => NavigationItem::query()
                ->where('location', 'header')
                ->whereNull('parent_id')
                ->whereKeyNot($navigationItem->id)
                ->orderBy('display_order')
                ->get(),
            'editingItem' => $navigationItem,
        ]);
    }

    public function update(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $validated = $this->validateItem($request, $navigationItem->id);
        $oldValues = $navigationItem->toArray();
        $navigationItem->update($validated);
        AuditLogger::record('navigation_item_updated', $navigationItem, $oldValues, $navigationItem->toArray(), request: $request);

        return redirect()->route('admin.navigation.index')->with('status', 'Navigation item updated.');
    }

    public function destroy(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $navigationItem->delete();
        AuditLogger::record('navigation_item_deleted', $navigationItem, request: $request);

        return back()->with('status', 'Navigation item deleted.');
    }

    public function restore(Request $request, int $navigationItem): RedirectResponse
    {
        $item = NavigationItem::withTrashed()->findOrFail($navigationItem);
        $item->restore();
        AuditLogger::record('navigation_item_restored', $item, request: $request);

        return back()->with('status', 'Navigation item restored.');
    }

    public function toggle(Request $request, NavigationItem $navigationItem): RedirectResponse
    {
        $navigationItem->update(['is_active' => ! $navigationItem->is_active]);
        AuditLogger::record('navigation_item_status_changed', $navigationItem, request: $request);

        return back()->with('status', 'Navigation item status updated.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['integer'],
        ]);

        foreach ($validated['orders'] as $id => $order) {
            NavigationItem::whereKey($id)->update(['display_order' => $order]);
        }

        AuditLogger::record('navigation_items_reordered', metadata: ['orders' => $validated['orders']], request: $request);

        return back()->with('status', 'Navigation order saved.');
    }

    private function validateItem(Request $request, ?int $ignoreId = null): array
    {
        $parentRules = [
            'nullable',
            Rule::exists('navigation_items', 'id'),
        ];

        if ($ignoreId) {
            $parentRules[] = Rule::notIn([$ignoreId]);
        }

        return $request->validate([
            'label_en' => ['required', 'string', 'max:255'],
            'label_bn' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'parent_id' => $parentRules,
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'open_in_new_tab' => ['nullable', 'boolean'],
        ]);
    }
}
