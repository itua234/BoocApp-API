<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\CustomResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\{LoginRequest};
use Illuminate\Support\Facades\{DB, Http};
use App\Models\{User, Dish, DishExtra, DishCategory};

class DishService
{   
    public function addDishAndExtra(Request $request)
    {
        $user = auth()->user();

        $url = NULL;
        if($request->hasFile('image')):
            $photo = $request->file('image');
            $response = \Cloudinary\Uploader::upload($photo);
            $url = $response["url"];
        endif;

        $dish = $this->createDish($request['dish'], $url, $user);

        if($request['extra']):
            $extra = $this->createExtra($request['extra'], $user);
        endif;

        $message = "Dish/Extra has been created successfully";
        return CustomResponse::success($message, $dish);
    }

    public function createDishCategory(Request $request)
    {
        $user = auth()->user();
        $category = DishCategory::create([
            'user_id' => $user->id,
            'name' => $request['name'],
            'slug' => isset($request['slug']) ? $request['slug'] : NULL,
            'type' => $user->user_type
        ]);

        $message = "Category has been created successfully";
        return CustomResponse::success($message, $category);
    }

    public function getCategories($chefId)
    {
        $user = auth()->user();
        $categories = DishCategory::where([
            'user_id' => $chefId
        ])->orWhere([
            'type' => 'admin'
        ])->get();
        $message = "Categories:";
        return CustomResponse::success($message, $categories);
    }

    public function getDishes($categoryId, $chefId)
    {
        //$user = auth()->user();
        $dishes = Dish::where([
            'chef_id' => $chefId,
            'category_id' => $categoryId
        ])->get();
        $message = "Dishes:";
        return CustomResponse::success($message, $dishes);
    }

    public function getExtras($chefId)
    {
        //$user = auth()->user();
        $extras = DishExtra::where([
            'chef_id' => $chefId
        ])->get();
        $message = "Extras:";
        return CustomResponse::success($message, $extras);
    }

    public function createDish(array $data, $url, $user)
    {
        $dish = Dish::create([
            'chef_id' => $user->id,
            'category_id' => (int) $data["category"],
            'name' => $data["name"],
            'description' => $data["description"],
            'price' => $data["price"],
            'profit' => $data["profit"],
            'measurement' => $data["measurement"],
            'image' => $url
        ]);

       return $dish;
    }

    public function createExtra(array $data, $user)
    {
        $extra = DishExtra::create([
            'chef_id' => $user->id,
            'name' => $data["name"],
            'price' => $data["price"],
            'profit' => $data["profit"],
            'measurement' => $data["measurement"],
            'description' => $data["description"]
        ]);

        return $extra;
    }

    public function addExtra(Request $request)
    {
        $user = auth()->user();
        $data = [
            'name' => $request["name"],
            'price' => $request["price"],
            'profit' => $request["profit"],
            'measurement' => $request["measurement"],
            'description' => $request["description"]
        ];

        $extra = $this->createExtra($data, $user);
        $message = "Extra Details:";
        return CustomResponse::success($message, $extra);
    }

    public function addDish(Request $request)
    {
        $user = auth()->user();

        $url = NULL;
        if($request->hasFile('image')):
            $photo = $request->file('image');
            $response = \Cloudinary\Uploader::upload($photo);
            $url = $response["url"];
        endif;
        $data = [
            'category' => $request["category"],
            'name' => $request["name"],
            'description' => $request["description"],
            'price' => $request["price"],
            'profit' => $request["profit"],
            'measurement' => $request["measurement"]
        ];

        $dish = $this->createDish($data, $url, $user);
        $message = "Dish Details:";
        return CustomResponse::success($message, $dish);
    }
}
