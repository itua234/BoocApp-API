<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Util\CustomResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\{
    CreateDish, 
    CreateExtra
};
use Illuminate\Support\Facades\{
    DB, 
    Http
};
use App\Models\{
    User, 
    Dish, 
    DishExtra, 
    DishCategory
};

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

        $data = [
            'dish' => $dish,
            'extra' => isset($extra) ? $extra : NULL
        ];

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
        $categories = DishCategory::with('dishes')->where([
            'user_id' => (int) $chefId
        ])->orWhere([
            'type' => 'admin'
        ])->get();
        $message = "Categories:";
        return CustomResponse::success($message, $categories);
    }

    public function getDishes($chefId, $categoryId)
    {
        $dishes = Dish::where([
            'chef_id' => (int) $chefId,
            'category_id' => (int) $categoryId
        ])->get();
        $message = "Dishes:";
        return CustomResponse::success($message, $dishes);
    }

    public function getExtras($chefId)
    {
        $extras = DishExtra::where([
            'chef_id' => (int) $chefId
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

    public function addExtra(CreateExtra $request)
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

    public function addDish(CreateDish $request)
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
