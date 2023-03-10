<?php

namespace App\Http\Controllers;
use App\Models\shirtsize;
use App\Models\shirtcolor;
use App\Models\orderdetail;
use App\Models\color;
use App\Models\order;
use App\Models\transport;
use App\Models\block;
use App\Models\freight;
use App\Models\status;
use App\Models\sample;
use App\Models\payment;
use App\Models\usershop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OrderAdminController extends Controller
{
    public function index(){
        
        $order = order::join('usershops','orders.id_user','=','usershops.id_user')
        ->join('statuses','orders.id_status','=','statuses.id_status')
         
        ->get(['id_order','user_name','user_fname','order_orderdate','order_id','status_name']);
         
        return view('Backend.order', compact(['order']));
    }

    public function payment(){
        
        $order = order::join('usershops','orders.id_user','=','usershops.id_user')
        ->join('statuses','orders.id_status','=','statuses.id_status')
        ->join('payments','payments.id_order','=','orders.id_order')
        ->get();
        
        return view('Backend.purchase', compact(['order']));
    }

    public function detail(Request $request){
        $order1 = order::where('id_order',$request->id)->get();
         
         if($order1[0]->id_sample != ''){
        $order = order::
        join('usershops','orders.id_user','=','usershops.id_user')
        ->join('statuses','orders.id_status','=','statuses.id_status')
        ->join('orderdetails','orders.id_order','=','orderdetails.id_order')
        ->join('transports','transports.id_tramsport','=','orders.id_post')
        ->join('shirtcolors','orders.id_shirtcolor','=','shirtcolors.id_shirtcolor')
        ->join('shirtsizes','orderdetails.id_shirtprice','=','shirtsizes.id_shirtsize')
        ->join('samples','orders.id_sample','=','samples.id_sample')
        ->where('orders.id_order',$request->id)->orderBy('orderdetails.id_order','ASC')->get();
         }else{
            $order = order::
        join('usershops','orders.id_user','=','usershops.id_user')
        ->join('statuses','orders.id_status','=','statuses.id_status')
        ->join('orderdetails','orders.id_order','=','orderdetails.id_order')
        ->join('transports','transports.id_tramsport','=','orders.id_post')
        ->join('shirtcolors','orders.id_shirtcolor','=','shirtcolors.id_shirtcolor')
        ->join('shirtsizes','orderdetails.id_shirtprice','=','shirtsizes.id_shirtsize')
         
        ->where('orders.id_order',$request->id)->orderBy('orderdetails.id_order','ASC')->get();
         }
        $status = status::all();
        $transport = transport::all();
         
        return view('Backend.test2-2', compact(['order','status','transport']));
    }

    public function edit(Request $request){
        $imageName="";
        
         $image = $request->sample->getClientOriginalName();
         
        
         
          
             $generate = hexdec(uniqid());
             $imageName = time() . '.' . $request->sample->extension();
              
             $request->sample->move(public_path('assets/images/'), $imageName);
          

        $order1 = orderdetail::where('id_order',$request->id)->orderBy('id_orderdetail','ASC')->get();
        $order = order::join('usershops','orders.id_user','=','usershops.id_user')
        ->join('statuses','orders.id_status','=','statuses.id_status')
        ->get(['id_order','user_name','user_fname','order_orderdate','order_id','status_name']);
        $i=0;
        $sum=0;
        if($request->type==2){
        foreach($order1 as $key => $orders){
             
            orderdetail::where('id_orderdetail',$request->idorder[$i])->orderBy('id_order','ASC')->update([
                'orderdetail_price' => $request->appraise[$i],
            ]);
            $sum=$sum+$request->appraise[$i];
            $i++;
        }
        order::where('id_order',$request->id)->update([
            'order_price' => $sum,
        ]);
    }

        $sample = sample::create([
            'id_order' => $request->id,
            'sample_picture' => $imageName,
        ]);
        
         order::where('id_order',$request->id)->update([
            'postcode' => $request->serial,
            'id_sample' => $sample->id,
            'id_status' => $request->option,
            'id_post' => $request->tp,
        ]);
        return redirect()->route('orderadmin');
    }
}
