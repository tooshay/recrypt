<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Data;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DataController extends Controller
{
    public function store(): JsonResponse
    {
        $data = Data::firstOrNew(['id' => request('id')]);

        $encrypter = new Encrypter(request('encryption_key'));
        $data->value = $encrypter->encrypt(request('value'));

        if ($data->save()) {
            return response()->json([
                'data stored successfully'
            ]);
        }

        return response()->json([
            'error storing data'
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function show(string $id): JsonResponse
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
