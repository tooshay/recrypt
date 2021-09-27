<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RetrieveRequest;
use App\Http\Requests\StoreRequest;
use App\Models\Data;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DataController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $encrypter = new Encrypter($validated['encryption_key']);

        $data = Data::firstOrNew(['id' => $validated['id']]);

        $data->value = $encrypter->encrypt($validated['value']);

        if ($data->save()) {
            return response()->json([
                'data stored successfully'
            ]);
        }

        return response()->json([
            'error storing data'
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function show(RetrieveRequest $request, string $id): JsonResponse
    {
        if (\Str::contains($id, '*')) {
            $id = \Str::of($id)->explode('*');
            $data = Data::where('id', 'LIKE', '%' . $id->first() . '%');
        } else {
            $data = Data::where('id', '=', $id);
        }

        try {
            $encrypter = new Encrypter(request('decryption_key'));
            $values = $data->get()->map(function ($value) use ($encrypter) {
                return $encrypter->decrypt($value->value);
            });
        } catch (\Exception $e) {
            Log::alert($e->getMessage(), ['decryption_key' => request('decryption_key')]);
            return response()->json([]);
        }

        return response()->json($values);
    }
}
