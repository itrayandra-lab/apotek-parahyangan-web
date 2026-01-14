<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpertQuoteFormRequest;
use App\Models\ExpertQuote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ExpertQuoteController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $expertQuote = ExpertQuote::query()->first();

        if ($expertQuote !== null) {
            return redirect()->route('admin.expert-quotes.edit', $expertQuote);
        }

        return redirect()->route('admin.expert-quotes.create');
    }

    public function create(): View
    {
        return view('admin.expert-quotes.create');
    }

    public function store(ExpertQuoteFormRequest $request): RedirectResponse
    {
        if (ExpertQuote::query()->exists()) {
            $existing = ExpertQuote::query()->first();

            return redirect()
                ->route('admin.expert-quotes.edit', $existing)
                ->with('error', 'Only one expert quote is allowed. Please update the existing quote.');
        }

        $validated = $request->validated();

        $expertQuote = ExpertQuote::create([
            'quote' => $validated['quote'],
            'author_name' => $validated['author_name'],
            'author_title' => $validated['author_title'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->hasFile('image')) {
            $expertQuote->addMediaFromRequest('image')->toMediaCollection('expert_quote_images');
        }

        return redirect()
            ->route('admin.expert-quotes.index')
            ->with('success', 'Expert quote saved successfully.');
    }

    public function edit(ExpertQuote $expertQuote): View
    {
        return view('admin.expert-quotes.edit', compact('expertQuote'));
    }

    public function update(ExpertQuoteFormRequest $request, ExpertQuote $expertQuote): RedirectResponse
    {
        $validated = $request->validated();

        $expertQuote->update([
            'quote' => $validated['quote'],
            'author_name' => $validated['author_name'],
            'author_title' => $validated['author_title'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->hasFile('image')) {
            $expertQuote->clearMediaCollection('expert_quote_images');
            $expertQuote->addMediaFromRequest('image')->toMediaCollection('expert_quote_images');
        }

        return redirect()
            ->route('admin.expert-quotes.index')
            ->with('success', 'Expert quote updated successfully.');
    }

    public function destroy(ExpertQuote $expertQuote): RedirectResponse
    {
        $expertQuote->delete();

        return redirect()
            ->route('admin.expert-quotes.index')
            ->with('success', 'Expert quote deleted successfully.');
    }
}
