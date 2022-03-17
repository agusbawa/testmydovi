<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Support\Facades\DB;
use stdClass;

class OrderControllerApi extends Controller
{

    protected function validatorOrder(array $data) {
        $errorMSg = [
            'NamaPelanggan.required' => "NamaPelanggan Diperlukan",
            'NamaPelanggan.string' => "NamaPelanggan Dalam Bentuk kalimat",
            'NamaPelanggan.max' => "NamaPelanggan maksimal 255 karakter",
            'Tanggal.required' => "Tanggal Diperlukan",
            'Tanggal.date_format' => "Tanggal Dalam format YYYY-mm-dd",
            'Jam.required' => "Jam Diperlukan",
            'Jam.date_format' => "Jam Dalam format HH:ii",
            'Total.required' => "Total Diperlukan",
            'Total.numeric' => "Total Dalam format angka",
            'BayarTunai.required' => "BayarTunai Diperlukan",
            'BayarTunai.numeric' => "BayarTunai Dalam format angka",
            'Kembali.required' => "Kembali Diperlukan",
            'Kembali.numeric' => "Kembali Dalam format angka",
        ];

        $validator = [
            'NamaPelanggan' => 'required|string|max:255',
            'Tanggal' => 'required|date_format:Y-m-d',
            'Jam' => 'required|date_format:H:i',
            'Total' => 'required|numeric',
            'BayarTunai' => 'required|numeric',
            'Kembali' => 'required|numeric'
        ];


        return Validator::make( $data, $validator, $errorMSg);
    }

    protected function validatorDetailOrder(array $data) {
        $errorMSg = [
            'NamaPelanggan.required' => "NamaPelanggan Diperlukan",
            'NamaPelanggan.string' => "NamaPelanggan Dalam Bentuk kalimat",
            'NamaPelanggan.max' => "NamaPelanggan maksimal 255 karakter",
            'Tanggal.required' => "Tanggal Diperlukan",
            'Tanggal.date_format' => "Tanggal Dalam format YYYY-mm-dd",
            'Jam.required' => "Jam Diperlukan",
            'Jam.date_format' => "Jam Dalam format HH:ii",
            'Total.required' => "Total Diperlukan",
            'Total.numeric' => "Total Dalam format angka",
            'BayarTunai.required' => "BayarTunai Diperlukan",
            'BayarTunai.numeric' => "BayarTunai Dalam format angka",
            'Kembali.required' => "Kembali Diperlukan",
            'Kembali.numeric' => "Kembali Dalam format angka",
        ];

        $validator = [
            'produk_id' => 'required',
            'Qty' => 'required|numeric',
            'HargaSatuan' => 'required|numeric',
            'SubTotal' => 'required|numeric'
        ];


        return Validator::make( $data, $validator, $errorMSg);
    }

    protected function orderParse($order){
        if($order){
            $data = new stdClass();
            $data->NamaPelanggan = $order->nama_pelanggan;
            $data->Tanggal = Carbon::parse($order->tanggal_order)->format('Y-m-d');
            $data->Jam = Carbon::parse($order->tanggal_order)->format('H:i');
            $data->Total = $order->total;
            $data->BayarTunai = $order->total_pembayaran;
            $data->Kembali = $order->total_kembalian;
            $data->DetilPenjualan = [];

            foreach($order->orderDetail as $detail){
                $detailRes = new stdClass();
                $detailRes->produk_id = $detail->id_order;
                $detailRes->Qty = $detail->qty;
                $detailRes->HargaSatuan = $detail->harga_satuan;
                $detailRes->SubTotal = $detail->sub_total;
                $data->DetilPenjualan[] = $detailRes;
            }
        }

        return $data;
    }

    protected function getOrder($orderId){
        $data = [];

        $order = Order::find($orderId);
        if($order){
            $data = $this->orderParse($order);
        }

        return $data;
    }


    public function newOrder(Request $request){
        $input = $request->all();
        $response = [];
        $status = false;

        $isValidate = $this->validatorOrder($input);
        if ($isValidate->fails()) {
            $response = $isValidate->errors();
        }else{

            DB::beginTransaction();
            try{
                $newOrder = new Order();
                $newOrder->nama_pelanggan = $input['NamaPelanggan'];
                $newOrder->tanggal_order = Carbon::parse($input['Tanggal'].' '.$input['Jam'])->format('Y-m-d H:i:s');
                $newOrder->total = $input['Total'];
                $newOrder->total_pembayaran = $input['BayarTunai'];
                $newOrder->total_kembalian = $input['Kembali'];
                $newOrder->save();

                if(isset($newOrder->id) && isset($input['DetilPenjualan'])){
                    $totalBelanja = 0;
                    foreach($input['DetilPenjualan'] as $detail){
                        if($this->validatorDetailOrder($detail)){
                            if(Produk::find($detail['produk_id'])){
                                $newDetail = new OrderDetail();
                                $newDetail->id_order = $newOrder->id;
                                $newDetail->id_produk = $detail['produk_id'];
                                $newDetail->qty = $detail['Qty'];
                                $newDetail->harga_satuan = $detail['HargaSatuan'];
                                $newDetail->sub_total = $newDetail->qty * $newDetail->harga_satuan;
                                $newDetail->save();
                                $totalBelanja = $totalBelanja + $newDetail->sub_total;
                            }
                        }
                    }
                }


                DB::commit();

                if($newOrder->total <> $totalBelanja){
                    $updateOrder = Order::find($newOrder->id);
                    if($updateOrder){
                        $updateOrder->total = $totalBelanja;
                        $updateOrder->total_kembalian = $newOrder->total_pembayaran - $totalBelanja;
                        $updateOrder->save();
                    }
                }

                $status = true;
                $response = $this->getOrder($newOrder->id);

            }catch(\Exception $e){
                DB::rollback();
                $response = ['msg' => 'Database Error'];
                $status = false;
            }
        }

        if($status){
            return response()->json(['status'=> $status, 'result' => $response],200);
        }else{
            return response()->json(['status'=> $status, 'result' => $response],400);
        }


    }


    public function listOrder(){
        $orderdata = Order::all();
        $response = [];
        foreach($orderdata as $order){
            $response[] = $this->orderParse($order);
        }

        return response()->json($response,200);
    }

    public function detailOrder($id){

        $response = $this->getOrder($id);

        return response()->json($response,200);
    }
}
