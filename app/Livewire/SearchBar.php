<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class SearchBar extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->search();
            $this->showResults = true;
        } else {
            $this->results = [];
            $this->showResults = false;
        }
    }

    public function search()
    {
        $products = Product::where('name', 'like', '%' . $this->query . '%')
            ->limit(8)
            ->get();

        $this->results = $products->map(function ($product) {
            return [
                'name' => $product->name,
                'price' => $product->price,
                'id' => $product->id,
            ];
        })->toArray();
    }

    public function clearSearch()
    {
        $this->reset(['query', 'results', 'showResults']);

        // Force re-render komponen
        $this->dispatch('$refresh');

        // Focus kembali ke input
        $this->dispatch('focusSearch');
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function selectProduct($productId)
    {
        $this->hideResults();
        // Add your product selection logic here
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
