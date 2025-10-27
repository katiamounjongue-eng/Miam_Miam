<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Afficher tout le menu avec filtres et recherche
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = item::with('type');

        // Filtre par type d'item
        if ($request->has('item_type_id')) {
            $query->where('item_type_id', $request->item_type_id);
        }

        // Filtre par disponibilité (en stock ou épuisé)
        if ($request->has('available')) {
            if ($request->available === 'true' || $request->available === '1') {
                $query->where('quantity', '>', 0);
            } else {
                $query->where('quantity', '=', 0);
            }
        }

        // Recherche par nom ou description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par prix
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Menu récupéré avec succès.',
            'data' => $items
        ]);
    }

    /**
     * Ajouter un nouveau plat au menu
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function additem(Request $request)
    {
        $validated = $request->validate([
            'item_type_id' => 'required|exists:item_type,item_type_id',
            'name' => 'required|string|max:255|unique:item,name',
            'description' => 'required|string|max:500',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'image_link' => 'nullable|url|max:255',
            
        ], [
            'name.unique' => 'Un plat avec ce nom existe déjà dans le menu.',
            'item_type_id.exists' => 'Le type d\'article sélectionné n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
        ]);

        DB::beginTransaction();

        try {
            // Générer l'ID automatique (format: IT000001)
            $lastitem = item::orderBy('item_id', 'desc')->first();
            $number = $lastitem ? intval(substr($lastitem->item_id, 2)) + 1 : 1;
            $itemId = 'IT' . str_pad($number, 6, '0', STR_PAD_LEFT);

            // Gérer l'upload de l'image
            $imageLink = $validated['image_link'] ?? null;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $itemId . '_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('menu_items', $imageName, 'public');
                $imageLink = Storage::url($imagePath);
            }

            // Créer le plat
            $item = item::create([
                'item_id' => $itemId,
                'item_type_id' => $validated['item_type_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'image_link' => $imageLink,
                            ]);

            $item->load('type');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plat ajouté au menu avec succès.',
                'data' => $item
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du plat.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un plat spécifique
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $item = item::with('type')->findOrFail($id);

        // Statistiques du plat
        $stats = $this->getitemStatistics($id);

        return response()->json([
            'success' => true,
            'data' => [
                'item' => $item,
                'statistics' => $stats
            ]
        ]);
    }

    /**
     * Mettre à jour un plat
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateitem(Request $request, string $id)
    {
        $item = item::findOrFail($id);

        $validated = $request->validate([
            'item_type_id' => 'sometimes|exists:item_type,item_type_id',
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('item')->ignore($item->item_id, 'item_id')],
            'description' => 'sometimes|required|string|max:500',
            'quantity' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_link' => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Gérer l'upload de la nouvelle image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($item->image_link && str_contains($item->image_link, 'storage/menu_items')) {
                    $oldImagePath = str_replace('/storage/', '', $item->image_link);
                    Storage::disk('public')->delete($oldImagePath);
                }

                $image = $request->file('image');
                $imageName = $item->item_id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('menu_items', $imageName, 'public');
                $validated['image_link'] = Storage::url($imagePath);
            }

            $item->update($validated);
            $item->load('type');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plat mis à jour avec succès.',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du plat.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un plat du menu
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteitem(string $id)
    {
        $item = item::findOrFail($id);

        // Vérifier si le plat a été commandé
        if ($item->orderitems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce plat car il a déjà été commandé. Vous pouvez le marquer comme indisponible.'
            ], 409);
        }

        DB::beginTransaction();

        try {
            // Supprimer l'image si elle existe
            if ($item->image_link && str_contains($item->image_link, 'storage/menu_items')) {
                $imagePath = str_replace('/storage/', '', $item->image_link);
                Storage::disk('public')->delete($imagePath);
            }

            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plat supprimé du menu avec succès.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du plat.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer un plat comme indisponible/disponible
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAvailability(string $id)
    {
        $item = item::findOrFail($id);

        $newStatus = !$item->is_available;
        $item->update(['is_available' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'Plat marqué comme disponible.' : 'Plat marqué comme indisponible.',
            'data' => [
                'item_id' => $item->item_id,
                'name' => $item->name,
                'is_available' => $newStatus
            ]
        ]);
    }

    /**
     * Mettre à jour le stock d'un plat
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request, string $id)
    {
        $item = item::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
            'action' => 'sometimes|in:set,add,subtract',
        ]);

        $action = $request->get('action', 'set');
        $quantity = $request->quantity;

        switch ($action) {
            case 'add':
                $item->increment('quantity', $quantity);
                $message = "{$quantity} unités ajoutées au stock.";
                break;
            case 'subtract':
                if ($item->quantity < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuffisant pour cette opération.'
                    ], 400);
                }
                $item->decrement('quantity', $quantity);
                $message = "{$quantity} unités retirées du stock.";
                break;
            case 'set':
            default:
                $item->update(['quantity' => $quantity]);
                $message = "Stock mis à jour à {$quantity} unités.";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'item_id' => $item->item_id,
                'name' => $item->name,
                'quantity' => $item->quantity
            ]
        ]);
    }

    /**
     * Mettre en avant / retirer de la mise en avant
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFeatured(string $id)
    {
        $item = item::findOrFail($id);

        $newStatus = !$item->is_featured;
        $item->update(['is_featured' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'Plat mis en avant.' : 'Plat retiré de la mise en avant.',
            'data' => [
                'item_id' => $item->item_id,
                'name' => $item->name,
                'is_featured' => $newStatus
            ]
        ]);
    }

    /**
     * Obtenir les plats mis en avant
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeatureditems()
    {
        $items = item::with('type')
            ->where('is_featured', true)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Dupliquer un plat
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function duplicateitem(string $id)
    {
        $originalitem = item::findOrFail($id);

        // Générer un nouvel ID
        $lastitem = item::orderBy('item_id', 'desc')->first();
        $number = $lastitem ? intval(substr($lastitem->item_id, 2)) + 1 : 1;
        $newitemId = 'IT' . str_pad($number, 6, '0', STR_PAD_LEFT);

        // Créer un nouveau nom unique
        $baseName = $originalitem->name . ' (Copie)';
        $newName = $baseName;
        $counter = 1;

        while (item::where('name', $newName)->exists()) {
            $newName = $baseName . ' ' . $counter;
            $counter++;
        }

        // Dupliquer le plat
        $newitem = $originalitem->replicate();
        $newitem->item_id = $newitemId;
        $newitem->name = $newName;
        $newitem->is_featured = false; // La copie n'est pas mise en avant par défaut
        $newitem->quantity = 0; // La copie commence avec un stock de 0
        $newitem->save();

        $newitem->load('type');

        return response()->json([
            'success' => true,
            'message' => 'Plat dupliqué avec succès.',
            'data' => $newitem
        ], 201);
    }

    /**
     * Obtenir les plats par catégorie
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenuByCategory()
    {
        $categories = itemType::with(['items' => function ($query) {
            $query->where('is_available', true)
                  ->orderBy('name');
        }])->get();

        $menu = $categories->map(function ($category) {
            return [
                'category_id' => $category->item_type_id,
                'category_name' => $category->item_type_name,
                'items_count' => $category->items->count(),
                'items' => $category->items
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Recherche avancée dans le menu
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function advancedSearch(Request $request)
    {
        $query = item::with('type');

        // Recherche par mots-clés (nom, description, ingrédients)
        if ($request->has('keywords')) {
            $keywords = $request->keywords;
            $query->where(function ($q) use ($keywords) {
                $q->where('name', 'LIKE', "%{$keywords}%")
                  ->orWhere('description', 'LIKE', "%{$keywords}%")
                  ->orWhere('ingredients', 'LIKE', "%{$keywords}%");
            });
        }

        // Filtre par catégories multiples
        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereIn('item_type_id', $request->categories);
        }

        // Filtre par plage de prix
        if ($request->has('price_range')) {
            $range = explode('-', $request->price_range);
            if (count($range) === 2) {
                $query->whereBetween('price', [(float)$range[0], (float)$range[1]]);
            }
        }

        

        $items = $query->where('is_available', true)->get();

        return response()->json([
            'success' => true,
            'results_count' => $items->count(),
            'data' => $items
        ]);
    }

    /**
     * Obtenir les statistiques d'un plat
     */
    private function getitemStatistics(string $itemId)
    {
        $orderitems = DB::table('order_item')
            ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
            ->where('order_item.item_id', $itemId)
            ->select(
                DB::raw('COUNT(DISTINCT order_item.order_id) as total_orders'),
                DB::raw('SUM(order_item.item_quantity) as total_quantity_sold'),
                DB::raw('MIN(orders.order_date) as first_order_date'),
                DB::raw('MAX(orders.order_date) as last_order_date')
            )
            ->first();

        $item = item::find($itemId);
        $totalRevenue = $orderitems->total_quantity_sold * $item->price;

        return [
            'total_orders' => (int) ($orderitems->total_orders ?? 0),
            'total_quantity_sold' => (int) ($orderitems->total_quantity_sold ?? 0),
            'total_revenue' => (float) $totalRevenue,
            'current_stock' => (int) $item->quantity,
            'first_order_date' => $orderitems->first_order_date,
            'last_order_date' => $orderitems->last_order_date,
        ];
    }

    /**
     * Export du menu (CSV ou JSON)
     * 
     * @param Request $request
     * @return mixed
     */
    public function exportMenu(Request $request)
    {
        $format = $request->get('format', 'json');
        $items = item::with('type')->get();

        if ($format === 'csv') {
            $filename = 'menu_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            $callback = function() use ($items) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Nom', 'Type', 'Description', 'Prix', 'Stock', 'Disponible']);

                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->item_id,
                        $item->name,
                        $item->type->item_type_name,
                        $item->description,
                        $item->price,
                        $item->quantity,
                        $item->is_available ? 'Oui' : 'Non'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Format JSON par défaut
        return response()->json([
            'success' => true,
            'export_date' => now()->toIso8601String(),
            'total_items' => $items->count(),
            'data' => $items
        ]);
    }
}