<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slider;

class SliderController extends Controller
{
    public function saveslider(Request $request){

        $this->validate($request,[
            'description1' => 'required',
            'description2' => 'required',
            'image' => 'image|nullable|max:1999'
        ]);

        //getting file name with extension
        $fileNameWithExt = $request->file('image')->getClientOriginalName();

        //getting file
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        //getting extension
        $ext = $request->file('image')->getClientOriginalExtension();

        //file to Store
        $fileNameToStore = $fileName.'_'.time().'.'.$ext;

        //uploading image to laravel file
        $path = $request->file('image')->storeAs("public/slider_images", $fileNameToStore);


        $slider = new Slider();
        $slider->description1 = $request->input('description1');
        $slider->description2 = $request->input('description2');
        $slider->image = $fileNameToStore;

        $slider->save();

        return back()->with('status', "Votre Slider à été Ajouter avec Succés");
    }

    public function deleteslider($id){
        $slider = Slider::find($id);
        Storage::delete("public/slider_images/$slider->image");
        $slider->delete();

        return back()->with('status', "Votre Slider à été Supprimer avec Succés");
    }

    public function editslider($id){
        $slider = Slider::find($id);

        return view('admin.editslider')->with('slider', $slider);
    }

    public function updateslider(Request $request, $id){
        $slider = Slider::find($id);
        $slider->description1 = $request->input('description1');
        $slider->description2 = $request->input('description2');

        if($request->file('image')){

            $this->validate($request, [
                'image' => 'image|nullable|max:1999'
            ]);

            $fileNameWithExt = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $ext = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $fileName.'_'.time().'.'.$ext;

            Storage::delete("public/slider_images/$slider->image");

            $path = $request->file('image')->storeAs("public/slider_images", $fileNameToStore);

            $slider->image = $fileNameToStore;
        }

        $slider->update();

        return redirect('/admin/sliders')->with('status', "Votre Slider à été Modifier avec Succés");
    }

    public function unactivateslider($id){
        $slider = Slider::find($id);
        $slider->status = 0;

        $slider->update();
        return back();
    }
    public function activateslider($id){
        $slider = Slider::find($id);
        $slider->status = 1;

        $slider->update();
        return back();
    }
}
