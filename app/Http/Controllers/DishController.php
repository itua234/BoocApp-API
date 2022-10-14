<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DishService;
use App\Http\Requests\{
    CreateDish, 
    CreateExtra
};

class DishController extends Controller
{
    private DishService $dishService;

    public function __construct(DishService $dishService)
    {
        $this->dishService = $dishService;
    }

    public function addDishAndExtra(Request $request)
    {
        return $this->dishService->addDishAndExtra($request);
    }

    public function createDishCategory(Request $request)
    {
        return $this->dishService->createDishCategory($request);
    }

    public function getCategories($chefId)
    {
        return $this->dishService->getCategories($chefId);
    }

    public function getDishes($chefId, $categoryId)
    {
        return $this->dishService->getDishes($chefId, $categoryId);
    }

    public function getExtras($chefId)
    {
        return $this->dishService->getExtras($chefId);
    }

    public function addExtra(CreateExtra $request)
    {
        return $this->dishService->addExtra($request);
    }

    public function addDish(CreateDish $request)
    {
        return $this->dishService->addDish($request);
    }

}
