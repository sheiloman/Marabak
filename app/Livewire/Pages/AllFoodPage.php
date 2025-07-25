<?php

namespace App\Livewire\Pages;

use App\Models\Foods;
use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Layout;
use App\Livewire\Traits\CategoryFilterTrait;

class AllFoodPage extends Component
{
    use CategoryFilterTrait;

    public $categories;
    public $selectedCategories = [];
    public $items;
    public $title = "All Foods";

    public function mount(Foods $foods)
    {
        $this->categories = Category::all();
        $this->items = $foods->getAllFoods();

    }

    #[Layout('livewire.components.layouts.page')]
    public function render()
    {
        $filteredProducts = $this->getFilteredItems();

        return view('livewire.pages.all-food-page', [
            'filteredProducts' => $filteredProducts,
        ]);
    }
}
