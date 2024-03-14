<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Models\Slider;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Client;
use App\Models\Order;


class ClientController extends Controller
{
    public function home(){
        $sliders = Slider::where('status', 1)->get();
        $products = Product::where('status', 1)->get();
        return view('client.home')->with('sliders', $sliders)->with('products', $products);
    }

    public function shop(){
        $products = Product::where('status', 1)->get();
        return view('client.shop')->with('products', $products);
    }

    public function cart(){
        return view('client.cart');
    }

    public function checkout(){
        if(Session::has('client')) return view('client.checkout');
        return redirect('/signin');
    }


    public function register(){
        return view('client.register');
    }

    public function createaccount(Request $request){
        $this->validate($request,[
            'email' => 'email|required|unique:clients',
            'password' => 'required|min:8'
        ]);

        $client = new Client();
        $client->email = $request->input('email');
        $client->password = bcrypt($request->input('password'));

        $client->save();

        return back()->with('status', "Your Account has been Created Successfully");
    }

    public function accessaccount(Request $request){
        $this->validate($request,[
            'email' => 'email|required',
        ]);

        $client = Client::where('email', $request->email)->first();

        if($client){
            if(Hash::check($request->input('password'), $client->password)){
                Session::put('client', $client);
                return redirect('/shop');
            }
            return back()->with('error', "Wrong Email or Password");
        }

        return back()->with('error', "You don't have an Account with this Email");

    }


    public function signin(){
        return view('client.signin');
    }

    public function logout(){
        Session::forget('client');
        return back();
    }

    public function addtocart($id){

        $product = Product::find($id);
        $oldcart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldcart);
        $cart->add($product);
        Session::put('cart', $cart);
        Session::put('topCart', $cart->items);

        return back();
    }

    public function updateqty(Request $request, $id){

        $oldcart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldcart);
        $cart->updateqty($id, $request->qty);
        Session::put('cart', $cart);
        Session::put('topCart', $cart->items);

        return back();
    }

    public function removeitem($id){

        $oldcart = Session::get('cart');
        $cart = new Cart($oldcart);
        $cart->removeitem($id);
        Session::put('cart', $cart);
        Session::put('topCart', $cart->items);

        return back();
    }

    public function payer(Request $request){

        try{

            $oldcart = Session::has('cart') ? Session::get('cart') : null;
            $cart = new Cart($oldcart);

            $order = new Order();
            $order->names = $request->input('firstname'). ' ' .$request->input('lastname');
            $order->address = $request->input('address');
            $order->cart = serialize($cart);

            Session::put('order', $order);

            $checkoutData = $this->checkoutData();

            $provider = new ExpressCheckout();

            $response = $provider->setExpressCheckout($checkoutData);

            return redirect($response['paypal_link']);

        }
        catch(\Exception $e){
            return redirect('/cart')->with('status', "Votre Commande à été effectuée avec Succés");
        }

    }

    private function checkoutData(){

        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        $data['items'] = [];

        foreach($cart->items as $item){
            $itemDetails=[
                'name' => $item['product_name'],
                'price' => $item['product_price'],
                'qty' =>  $item['qty']
            ];
            $data['items'][] = $itemDetails;
        }

        $checkoutData = [
            'items' => $data['items'],
            'return_url' => url('/payementSuccess'),
            'cancel_url' => url('/cart'),
            'invoice_id' => uniqid(),
            'invoice_description' => "order_description",
            'total' => Session::get('cart')->totalPrice
        ];

        return $checkoutData;
    }

    public function payementSuccess(Request $request){

        try {
            $token = $request->get('token');
            $payerId = $request->get('PayerID');
            $checkoutData = $this->checkoutData();

            $provider = new ExpressCheckout();
            $response = $provider->getExpressCheckoutDetails($token);
            $response = $provider->doExpressCheckoutPayment($checkoutData, $token, $payerId);

            Session::get('order')->save();

            Session::forget('cart');
            Session::forget('topCart');

            return redirect('/cart')->with('status', "Votre commande à été effectuée avec Succés");



        } catch (\Exception $e) {
            return redirect('/cart')->with('status', $e->getMessage());
        }

    }
}
