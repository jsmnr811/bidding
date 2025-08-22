<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\GeomappingUsersDataTable;

class GeomappingUsersTableController extends Controller
{
    public function index(GeomappingUsersDataTable $dataTable)
    {
        return $dataTable->render('geomapping.iplan.user-list');
    }

    public function idCard($id)
{
    $user = \App\Models\GeomappingUser::findOrFail($id);

    return view('geomapping.iplan.user-id-card', compact('user'));
}

}
