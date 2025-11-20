<?php

namespace App\Http\Controllers;

use App\Models\ExpenseRecipient;
use App\Models\OperationalCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseRecipientController extends Controller
{
    /**
     * Store a newly created expense recipient.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'operational_category_id' => 'required|exists:operational_categories,id',
        ]);

        $recipient = ExpenseRecipient::create($validated);
        $recipient->load('operationalCategory');

        return response()->json([
            'success' => true,
            'recipient' => [
                'id' => $recipient->id,
                'name' => $recipient->name,
                'category_name' => $recipient->operationalCategory->name,
            ],
        ]);
    }

    /**
     * Get recipients filtered by category.
     */
    public function byCategory(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return response()->json(['recipients' => []]);
        }

        $recipients = ExpenseRecipient::where('operational_category_id', $categoryId)
            ->orderBy('name')
            ->get()
            ->map(function ($recipient) {
                return [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                ];
            });

        return response()->json(['recipients' => $recipients]);
    }
}

