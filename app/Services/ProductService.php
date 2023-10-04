<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Models\order;
use App\Repository\Eloquent\PrescriptionRepository;
use App\Repository\Eloquent\studentRepository;
use App\Repository\Eloquent\patientRepository;
use Illuminate\Http\Request;
use App\Models\student;
use App\Models\prescription;
use App\Models\product;
use App\Models\store_manager;
use App\Models\student_discount;
use App\Repository\Eloquent\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class ProductService extends BaseController
{
    private $ProductRepository;

    public function __construct(ProductRepository $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }

    public function allProducts()
    {
        return $this->ProductRepository->allProducts();
    }

    public function addProduct(Request $request)
    {
        $user = auth('sanctum')->user();
        $storeMAnager = store_manager::where('user_id', $user->id)->first();

        return $this->ProductRepository->create([
            'name' => $request->name,
            'price' => $request->price,
            $file = Cloudinary::upload($request->file('photo')->getRealPath(), [
                'folder' => 'anas29December'
            ])->getSecurePath(),
            'photo' => $file,
            'Quantity' => $request->Quantity,
            'category' => $request->category,
            'store_manager_id' => $storeMAnager->id
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $storeMAnager = store_manager::where('user_id', $user->id)->first();
        if ($request->hasFile('photo')) {
            return $this->ProductRepository->update($id, [
                'name' => $request->name,
                'price' => $request->price,

                $file = Cloudinary::upload($request->file('photo')->getRealPath(), [
                    'folder' => 'anas29December'
                ])->getSecurePath(),
                'photo' => $file,
                'Quantity' => $request->Quantity,
                'category' => $request->category,
                'store_manager_id' => $storeMAnager->id
            ]);
        } else {
            return $this->ProductRepository->update($id, [
                'name' => $request->name,
                'price' => $request->price,
                'Quantity' => $request->Quantity,
                'category' => $request->category,
                'store_manager_id' => $storeMAnager->id
            ]);
        }
    }

    public function deleteProduct($id)
    {
        return  $this->ProductRepository->delete($id);
    }

    public function getAllOrders()
    {
        return order::with('students', 'products')->get();
    }

    public function showOrder($id)
    {
        return order::with('students', 'products')->find($id);
    }

    public function deleteOrder($id)
    {
        return order::find($id)->delete();
    }

    public function addOrder(Request $request)
    {
        $user = auth('sanctum')->user();
        $student = student::where('user_id', $user->id)->first();
        $discountValues = student_discount::all();
        $TheDiscount=null;
        foreach ($discountValues as $discountValue) {
            if ($discountValue->student_id == $student->id) {
                $TheDiscount = $discountValue->value;
            }
        }
        foreach ($request->products as $product) {
            $order = order::create([
                'product_id' => $product['id'],
                'student_id' => $student->id,
                'Quantity' => $product['quantity'],
                'discountValue' =>  $TheDiscount ? $TheDiscount.'%' : null
            ]);
        }

        return $order;
    }
    // public function addOrder(Request $request,$id)
    // {
    //     $product=product::find($id);
    //     $user = auth('sanctum')->user();
    //     $student = student::where('user_id', $user->id)->first();
    //     return order::create([
    //         'product_id'=>$product->id,
    //         'student_id'=>$student->id,
    //         'Quantity'=>$request->Quantity
    //     ]);
    // }
}
