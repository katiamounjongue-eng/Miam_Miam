<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Liste tous les articles avec leur type
     * GET /api/admin/items
     */
    public function index()
    {
        try {
            $items = Item::with('type')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des articles récupérée avec succès.',
                'data' => $items
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des articles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un nouvel article
     * POST /api/admin/items
     */
    public function storeItem(Request $request)
    {
        try {
            $request->validate([
                'item_type_id' => 'required|exists:item_type,item_type_id',
                'name' => 'required|string|max:255|unique:item,name',
                'description' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0.01',
                'image_link' => 'required|url|max:255',
            ]);

            // Générer l'ID automatique
            $lastItem = Item::orderBy('item_id', 'desc')->first();
            $number = $lastItem ? intval(substr($lastItem->item_id, 2)) + 1 : 1;
            $itemId = 'IT' . str_pad($number, 6, '0', STR_PAD_LEFT);

            $item = Item::create(array_merge(
                ['item_id' => $itemId],
                $request->only(['item_type_id', 'name', 'description', 'quantity', 'price', 'image_link'])
            ));

            return response()->json([
                'success' => true,
                'message' => 'Article de menu créé avec succès.',
                'data' => $item->load('type')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'article.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche un article spécifique avec son type
     * GET /api/admin/items/{id}
     */
    public function showItem($id)
    {
        try {
            $item = Item::with('type')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Article récupéré avec succès.',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article introuvable.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Met à jour un article existant
     * PUT /api/admin/items/{id}
     */
    public function updateItem(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            $request->validate([
                'item_type_id' => 'sometimes|exists:item_type,item_type_id',
                'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('item')->ignore($item->item_id, 'item_id')],
                'description' => 'sometimes|required|string|max:255',
                'quantity' => 'sometimes|required|integer|min:0',
                'price' => 'sometimes|required|numeric|min:0.01',
                'image_link' => 'sometimes|required|url|max:255',
            ]);

            $item->update($request->only([
                'item_type_id', 'name', 'description', 'quantity', 'price', 'image_link'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Article de menu mis à jour avec succès.',
                'data' => $item->fresh('type')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'article.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un article, si aucun ordre ne le référence
     * DELETE /api/admin/items/{id}
     */
    public function destroyItem($id)
    {
        try {
            $item = Item::findOrFail($id);

            if ($item->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer : cet article a été commandé. Marquez-le comme épuisé.'
                ], 409);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article supprimé avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'article.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ MÉTHODE COMPLÈTE : Statistiques des plats les plus commandés
     * GET /api/admin/items/most-ordered?limit=10&period=month
     */
    public function getMostOrderedDishesStats(Request $request)
    {
        try {
            $request->validate([
                'limit' => 'sometimes|integer|min:1|max:100',
                'period' => 'sometimes|in:week,month,quarter,year,all',
            ]);

            $limit = $request->get('limit', 10);
            $period = $request->get('period', 'month');

            $dateFilter = $this->getDateFilter($period);

            // Requête pour obtenir les plats les plus commandés
            $query = DB::table('order_item')
                ->join('item', 'order_item.item_id', '=', 'item.item_id')
                ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
                ->select(
                    'item.item_id',
                    'item.name as dish_name',
                    'item.image_link',
                    'item.price',
                    DB::raw('SUM(order_item.item_quantity) as total_quantity_ordered'),
                    DB::raw('COUNT(DISTINCT order_item.order_id) as number_of_orders'),
                    DB::raw('SUM(order_item.item_quantity * item.price) as total_revenue')
                )
                ->groupBy('item.item_id', 'item.name', 'item.image_link', 'item.price')
                ->orderByDesc('total_quantity_ordered')
                ->limit($limit);

            // Appliquer le filtre de date si ce n'est pas "all"
            if ($period !== 'all') {
                $query->where('orders.order_date', '>=', $dateFilter);
            }

            $topDishes = $query->get();

            // Calculer les statistiques globales
            $totalOrders = Orders::when($period !== 'all', function($q) use ($dateFilter) {
                return $q->where('order_date', '>=', $dateFilter);
            })->count();

            $totalQuantitySold = DB::table('order_item')
                ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
                ->when($period !== 'all', function($q) use ($dateFilter) {
                    return $q->where('orders.order_date', '>=', $dateFilter);
                })
                ->sum('order_item.item_quantity');

            // Formater les données pour les graphiques
            $chartData = [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Quantité commandée',
                        'data' => [],
                        'backgroundColor' => [],
                        'borderColor' => [],
                    ]
                ]
            ];

            $detailedStats = [];
            $colors = $this->generateColors($topDishes->count());

            foreach ($topDishes as $index => $dish) {
                // Données pour le graphique
                $chartData['labels'][] = $dish->dish_name;
                $chartData['datasets'][0]['data'][] = (int) $dish->total_quantity_ordered;
                $chartData['datasets'][0]['backgroundColor'][] = $colors[$index]['bg'];
                $chartData['datasets'][0]['borderColor'][] = $colors[$index]['border'];

                // Statistiques détaillées
                $detailedStats[] = [
                    'rank' => $index + 1,
                    'item_id' => $dish->item_id,
                    'dish_name' => $dish->dish_name,
                    'image_link' => $dish->image_link,
                    'unit_price' => (float) $dish->price,
                    'total_quantity_ordered' => (int) $dish->total_quantity_ordered,
                    'number_of_orders' => (int) $dish->number_of_orders,
                    'total_revenue' => (float) $dish->total_revenue,
                    'average_quantity_per_order' => round($dish->total_quantity_ordered / $dish->number_of_orders, 2),
                    'percentage_of_total' => $totalQuantitySold > 0 
                        ? round(($dish->total_quantity_ordered / $totalQuantitySold) * 100, 2) 
                        : 0
                ];
            }

            return response()->json([
                'success' => true,
                'period' => $period,
                'date_from' => $period !== 'all' ? $dateFilter : null,
                'date_to' => now()->format('Y-m-d'),
                'summary' => [
                    'total_orders' => $totalOrders,
                    'total_dishes_sold' => (int) $totalQuantitySold,
                    'unique_dishes_ordered' => $topDishes->count(),
                    'top_dish' => $topDishes->first()?->dish_name ?? 'N/A',
                    'total_revenue' => (float) $topDishes->sum('total_revenue')
                ],
                'chart_data' => $chartData,
                'detailed_statistics' => $detailedStats,
                'generated_at' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Méthode helper : Obtenir la date de début selon la période
     */
    private function getDateFilter(string $period): ?string
    {
        if ($period === 'all') {
            return null;
        }

        return match($period) {
            'week' => now()->subWeek()->format('Y-m-d'),
            'month' => now()->subMonth()->format('Y-m-d'),
            'quarter' => now()->subMonths(3)->format('Y-m-d'),
            'year' => now()->subYear()->format('Y-m-d'),
            default => now()->subMonth()->format('Y-m-d'),
        };
    }

    /**
     * ✅ Méthode helper : Générer des couleurs pour les graphiques
     */
    private function generateColors(int $count): array
    {
        $baseColors = [
            ['bg' => 'rgba(255, 99, 132, 0.6)', 'border' => 'rgba(255, 99, 132, 1)'],
            ['bg' => 'rgba(54, 162, 235, 0.6)', 'border' => 'rgba(54, 162, 235, 1)'],
            ['bg' => 'rgba(255, 206, 86, 0.6)', 'border' => 'rgba(255, 206, 86, 1)'],
            ['bg' => 'rgba(75, 192, 192, 0.6)', 'border' => 'rgba(75, 192, 192, 1)'],
            ['bg' => 'rgba(153, 102, 255, 0.6)', 'border' => 'rgba(153, 102, 255, 1)'],
            ['bg' => 'rgba(255, 159, 64, 0.6)', 'border' => 'rgba(255, 159, 64, 1)'],
        ];

        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }
}