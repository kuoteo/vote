<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Repositories\Eloquent\AdminUserRepositoryEloquent;

use App\Repositories\Eloquent\AdminUserRepositoryEloquent as AdminUserRepository;

use App\Repositories\Eloquent\VoteModelRepositoryEloquent;

use App\Repositories\Eloquent\VoteModelRepositoryEloquent as VoteModelRepository;

class AdminUserController extends Controller
{

    private $admin;

    public function __construct(AdminUserRepository $AdminUserRepository)
    {
        $this->admin = $AdminUserRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VoteModelRepository $VoteModelRepository)
    {
        $votelist = $VoteModelRepository->
                    showVoteList(Auth::user()->id);
        return view('admin.home', compact('votelist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
