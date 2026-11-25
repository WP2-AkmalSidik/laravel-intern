<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentApiController extends Controller
{
    public function index()
    {
        $departments = Cache::remember(
            'master_departments',
            3600,
            function () {
                return Department::query()
                    ->select(
                        'id',
                        'kode_departemen',
                        'nama_departemen',
                        'kepala_departemen'
                    )
                    ->orderBy('nama_departemen')
                    ->get()
                    ->toArray();
            }
        );

        return response()->json([
            'message' => 'Data departemen berhasil diambil.',
            'data' => $departments,
        ]);
    }
}
