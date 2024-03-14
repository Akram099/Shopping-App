<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Order;

class PdfController extends Controller
{
    public function seeorders($id){

        Session::put('id', $id);
        try {
            $pdf = \App::make('dompdf.wrapper')->setPaper('a4', 'landscape');
            $pdf->loadHTML($this->convert_orders_data_to_html());

            return $pdf->stream();

        }
        catch (Exception $e) {
            return redirect('/admin/orders')->with('error', $e->getMessage());
        }
    }

    public function convert_orders_data_to_html(){
        $orders = Order::where('id',Session::get('id'))->get();

        foreach($orders as $order){
            $nom = $order->names;
            $address = $order->address;
            $date = $order->created_at;
        }

        $orders->transform(function($order, $key){
            $order->cart = unserialize($order->cart);

            return $order;
        });

        $output = '<link rel="stylesheet" href="frontend/css/style1.css">
            <table class="table">
                <thead class="thead">
                    <tr class="text-left">
                        <th>Client Name : '.$nom.'<br> Client Address : '.$address.'<br> Date : '.$date.'</th>
                    </tr>
                </thead>
            </table>
            <table class="table">
                <thead class="thead-primary">
                    <tr class="thead-primary">
                        <th>Image</th>
                        <th>Product name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
                    foreach($orders as $order){
                        foreach($order->cart->items as $item){

                            $output .= '<tr class="text-center">
                                            <td class="image-prod"><img src="storage/product_images/'.$item['product_image'].'" alt ="" style ="height:80px; width:80px;"></td>
                                            <td class="product-name">
                                                <h3>'.$item['product_name'].'</h3>
                                            </td>
                                            <td class="price">$ '.$item['product_price'].'</td>
                                            <td class="qty">'.$item['qty'].'</td>
                                            <td class="total">$ '.$item['product_price']*$item['qty'].'</td>
                                        </tr>
                                        </tbody>';
                        }

                        $totalPrice = $order->cart->totalPrice;
                    }

                    $output .='</table>';

                    $output .='<table class="table">
                                <thead class="thead">
                                    <tr class="text-center">
                                        <th>Total</th>
                                        <th>$ '.$totalPrice.'</th>
                                    </tr>
                                </thead>
                                </table>';

                    return $output;
    }
}
