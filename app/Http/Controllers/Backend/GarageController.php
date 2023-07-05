<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Models\Garage;

class GarageController extends Controller
{
    public function AllGarage(){

        $garage = Garage::latest()->get();
        return view('backend.garage.all_garage',compact('garage'));
    } // End Method


    public function AddGarage(){
        return view('backend.garage.add_garage');
    } // End Method


    public function StoreGarage(Request $request){

        $validateData = $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:garages|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'image' => 'required',
        ]);

        $image = $request->file('image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
        Image::make($image)->resize(300,300)->save('upload/garage/'.$name_gen);
        $save_url = 'upload/garage/'.$name_gen;

        Garage::insert([

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $save_url,
            'created_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Garage Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.garage')->with($notification);
    } // End Method


    public function EditGarage($id){

        $garage = Garage::findOrFail($id);
        return view('backend.garage.edit_garage',compact('garage'));

    } // End Method


    public function UpdateGarage(Request $request){

        $garage_id = $request->id;

        if ($request->file('image')) {

            $image = $request->file('image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(300,300)->save('upload/garage/'.$name_gen);
            $save_url = 'upload/garage/'.$name_gen;

            Garage::findOrFail($garage_id)->update([

                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'shopname' => $request->shopname,
                'account_holder' => $request->account_holder,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'city' => $request->city,
                'image' => $save_url,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' => 'Garage Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.garage')->with($notification);

        } else{

            Garage::findOrFail($garage_id)->update([

                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'shopname' => $request->shopname,
                'account_holder' => $request->account_holder,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'city' => $request->city,
                'created_at' => Carbon::now(),

            ]);

            $notification = array(
                'message' => 'Garage Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.garage')->with($notification);

        } // End else Condition


    } // End Method


    public function DeleteGarage($id){

        $garage_img = Garage::findOrFail($id);
        $img = $garage_img->image;
        unlink($img);

        Garage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Garage Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    } // End Method
}
