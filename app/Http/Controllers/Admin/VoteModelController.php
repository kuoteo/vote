<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Auth;

use Cache;

use DB;

use App\Http\Requests\VoteModelRequest;

use App\Repositories\Eloquent\VoteModelRepositoryEloquent;

use App\Repositories\Eloquent\VoteModelRepositoryEloquent as VoteModelRepository;

use App\Repositories\Eloquent\BehalfRepositoryEloquent;

use App\Repositories\Eloquent\BehalfRepositoryEloquent as BehalfRepository;

use App\Repositories\Eloquent\VoteRepositoryEloquent;

use App\Repositories\Eloquent\VoteRepositoryEloquent as VoteRepository;

use App\Models\behalf;

use App\Models\vote;

use App\Models\VoteModel;

class VoteModelController extends Controller
{

	private $vote_model;

    public function __construct(
    	VoteModelRepository $VoteModelRepository,
        BehalfRepository $BehalfRepository,
        VoteRepository $VoteRepository)
    {
        $this->middleware('auth.votemodel')
                ->only(['show', 'edit', 'destroy', 'ShowVoteUrl']);
        $this->vote_model = $VoteModelRepository;
        $this->behalf = $BehalfRepository;
        $this->vote = $VoteRepository;
    }

    /**
     * Show Index
     * @author leekachung <[leekachung17@gmail.com]>
     * @DateTime        2018-10-02T20:04:18+0800
     * @return 
     */
    public function index($id)
	{
		return;
	}
    
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.vote.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoteModelRequest $request)
    {
        //Store: [success => boolean, false => array]
		$res = $this->vote_model
            ->createVoteModel($request, Auth::user()->id);

        if (is_array($res)) {
            flash($res['Content'])->error();
            return back()->withInput();
        }

		flash('新建投票项目成功');
		return redirect(route('admin.index.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $signnum = $this->behalf->showSignNum($id);

        $votepeople = $this->behalf->showVotePeople($id);

        $behalf = $this->behalf->showBehalfList($id);

        $vote = $this->vote->showCandidateList($id);

        return view('admin.vote.index', compact('id', 'signnum', 
                'votepeople', 'behalf', 'vote'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $res = $this->vote_model
                ->editVoteModel($id, Auth::user()->id);

        return view('admin.vote.edit', $res);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VoteModelRequest $request, $id)
    {
        $res = $this->vote_model->
        updateVoteModel($request,
            $id, Auth::user()->id);


        if (is_array($res)) {
            flash($res['Content'])->error();
            return back()->withInput();
        }

        //修改投票项目时间后 重置缓存 防止投票失效无法检测
        Cache::forget('vote_model_id'.$id);

        flash('操作成功');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->vote_model->delete($id);
        
        $this->behalf->deleteBehalfGather($id);

        $this->vote->deleteCandidateGather($id);
        
        flash('项目删除成功');
        return back();
    }

    /**
     * ShowVoteUrl 展示投票链接/二维码
     * @author leekachung <leekachung17@gmail.com>
     * @param  [type] $id [description]
     */
    public function ShowVoteUrl($id)
    {
        $res = $this->vote_model->CreateVoteUrl($id);

        return view('admin.vote.qrcode', compact('res'));
    }

    /**
     * flushCache 清空展示候选人API数据
     * @author leekachung <leekachung17@gmail.com>
     */
    public function flushCache()
    {
        $this->vote->flushCache();
        
        flash('清空缓存成功');
        return back();
    }

    /**
     * initApi 投票Api初始化
     * @author leekachung <leekachung17@gmail.com>
     * @param  [type] $id [description]
     */
    public function initApi($id)
    {
        //进入队列 若队列已满 0.3s后请求
        while (!$this->behalf->doQueue('Index', 150, 300000)) {
            usleep(300000);
        }

        echo "Loading...";

        //判断请求方式
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $res = [
            'id' => $id,
            'name' => $this->vote_model->showVoteMes($id),
            'url' => $http_type.$_SERVER["HTTP_HOST"].'/vote/api/'
        ];
        
        return view('admin.init', compact('res'));
    }

    /**
     * showRealtime 实时显示票数
     * @author leekachung <leekachung17@gmail.com>
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function showRealtime($id)
    {
        $url = route('show.init');
        return view('vote_realtime', compact('url', 'id'));
    }

    /**
     * getCandidateList 获取候选人列表
     * @author leekachung <leekachung17@gmail.com>
     * @return [type] [description]
     */
    public function getCandidateList(Request $request)
    {
        //进入队列 若队列已满 0.3s后请求
        while (!$this->vote->doQueue('Candidate', 150, 300000)) {
            usleep(300000);
        }

        $vote_model_id = $request->vote_model_id;

        return $this->vote->ReturnJsonResponse(
            200, $this->vote->getCandidateList($vote_model_id)
        );
    }

    /**
     * clearTest 清除所有测试数据
     * @author leekachung <leekachung17@gmail.com>
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function clearTest($id)
    {
        //开启事务
        DB::beginTransaction();
        try {
        	//清空代表 方便直接导表
        	//$this->behalf->deleteBehalfGather($id);
            //清除代表数据
            $this->behalf->clearTest($id);
            //清除候选人数据
            $this->vote->clearTest($id);
            //提交事务
            DB::commit();
            flash('操作成功');
        } catch (QueryException $e) {
            //事务回滚
            DB::rollback();
            flash('操作失败，请检查数据库')->error();
        }

        return back();
    }

    // public function searchBehalf($id)
    // {
    //     return $this->behalf->searchBehalf($id);
    // }

    /**
     * todoupdate tododelete 代表增删
     * @author kuoteo 894569910@qq.com>
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function todoupdate(Request $request,$id){
        $behalf = behalf::find($id);
        $vote_model_id=$behalf->vote_model_id;
        if($request->isMethod('post')){

            $this->validate($request,[
                'behalf.name'=>'required|min:2|max:20',
                'behalf.student_id'=>'required',
            ],[
                'required'=>':attribute 为必填项',
                'min'=>':attribute 长度不符合要求',
            ],[
                'behalf.name'=>'姓名',
                'behalf.student_id'=>'学号'
                ]);

            $data=$request->input('behalf');
            $behalf->name=$data['name'];
            $behalf->student_id=$data['student_id'];
            $behalf->is_sign=$data['is_sign'];
            $behalf->is_vote=$data['is_vote'];

            if($behalf->save()){
                flash('操作成功');
                return redirect()->route('admin.vote.update',$vote_model_id);
                //return back();
            }
        }
         return view('admin.TODO.update',[
             'behalf' => $behalf
             ]);
    }
    public function tododelete(Request $request,$id){
        $behalf=behalf::find($id);
        if($behalf->delete()){
            flash('删除成功！');
            return back();
        }
        else{
            flash('删除失败！');
            return back();
        }


    }

    /**
     * todoupdate tododelete 候选人增删
     * @author kuoteo 894569910@qq.com>
     * @param  [type] $id [description]
     * @return [type]     [description]
     */

    public function votetodoupdate(Request $request,$id){
        $Vote = vote::find($id);
        $vote_model_id=$Vote->vote_model_id;
        if($request->isMethod('post')){

            $this->validate($request,[
                'Vote.name'=>'required|min:2|max:20',
                'Vote.vote_id'=>'required',
            ],[
                'required'=>':attribute 为必填项',
                'min'=>':attribute 长度不符合要求',
            ],[
                'Vote.name'=>'姓名',
                'Vote.vote_id'=>'编号'
            ]);

            $data=$request->input('Vote');
            $Vote->vote_id=$data['vote_id'];
            $Vote->name=$data['name'];

            if($Vote->save()){
                flash('操作成功');
                return redirect()->route('admin.vote.update',$vote_model_id);
            }
        }
        return view('admin.TODO.vote_update',[
            'Vote'=>$Vote
        ]);
    }
    public function votetododelete(Request $request,$id){
        $Vote=vote::find($id);
        if($Vote->delete()){
            flash('删除成功');
            return back();
        }
        else{
            flash('删除失败');
            return back();
        }
    }

    public function editVotemodelStart(Request $request,$id){
            $time=strtotime('now');
            $VoteModel=voteModel::find($id);
            $VoteModel->start=$time;

            if($VoteModel->end<$VoteModel->start){
                $VoteModel->end=$VoteModel->start+86400;
            }

            if($VoteModel->save()){
		Cache::forget('vote_model_id'.$id);
                flash('操作成功 已开始投票 请注意修改停止时间');
                return back();
            }
            else{
                flash('操作失败');
                return back();
            }
    }

    public function editVotemodelEnd(Request $request,$id){
        $time=strtotime('now');
        $VoteModel=voteModel::find($id);
        $VoteModel->end=$time;
        if($VoteModel->save()){
	    Cache::forget('vote_model_id'.$id);
            flash('操作成功 已停止投票');
            return back();
        }
        else{
            flash('操作失败');
            return back();
        }
    }


}
