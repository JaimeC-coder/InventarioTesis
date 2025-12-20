<?php

namespace App\Http\Controllers\mobile\client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Response\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // $user = auth('jwt')->user();
        $customers = Customer::with('identity')->get();
        $anonymousResourceCollection =  \App\Http\Resources\Customer::collection($customers);
        return JsonResponse::success([
            'customers' => $anonymousResourceCollection,
        ], 'Autenticación exitosa');
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Client created']);
    }

    public function show(string $id)
    {
        $customer = Customer::where('uuid', $id)->with('identity', 'sales')->firstOrFail();
        // $anonymousResource = new \App\Http\Resources\Customer($customer);
        return JsonResponse::success([
            'customer' => $customer,
            // 'customer' => $anonymousResource,
        ], 'Client details retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => sprintf('Client with id: %s updated', $id)]);
    }

    public function destroy($id)
    {
        return response()->json(['message' => sprintf('Client with id: %s deleted', $id)]);
    }
}
