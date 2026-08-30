<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryMetricRequest;
use App\Services\Chatbot\ChatbotQueryService;
use Illuminate\Http\Request;


class ChatbotController extends Controller
{
    public function __construct(private ChatbotQueryService $service) {}

    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        return response()->json(
            $this->service->handleUserMessage($request->user(), $request->input('message'))
        );
    }

    public function executeMetric(QueryMetricRequest $request)
    {
        return response()->json(
            $this->service->runMetric($request->user(), $request->validated())
        );
    }
}
