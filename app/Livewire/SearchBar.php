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
                'slug' => $product->slug, // Tambahkan slug untuk keperluan lain
            ];
        })->toArray();
    }

    public function clearSearch()
    {
        $this->reset(['query', 'results', 'showResults']);
        $this->dispatch('$refresh');
        $this->dispatch('focusSearch');
    }

    public function hideResults()
    {
        $this->showResults = false;
    }

    public function selectProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $this->showResults = false;
        // Redirect ke halaman detail produk by slug
        return redirect()->route('products.show', $product->slug);
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
