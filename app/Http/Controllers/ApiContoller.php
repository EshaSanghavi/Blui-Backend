<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getData()
    {
        $data = Data::all();
        return response()->json($data);
    }

    public function createData(Request $request)
    {
        $data = Data::create($request->all());
        return response()->json($data, 201);
    }

    public function updateData(Request $request, $id)
    {
        $data = Data::findOrFail($id);
        $data->update($request->all());
        return response()->json($data, 200);
    }

    public function deleteData($id)
    {
        Data::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}