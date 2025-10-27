<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class itemStatsController extends Controller
{
    /**
     * Récupère les items les plus commandés avec statistiques détaillées
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostOrdereditems(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'period' => 'sometimes|in:week,month,quarter,year,all',
            'item_type_id' => 'sometimes|exists:item_type,item_type_id',
            'order_by' => 'sometimes|in:quantity,revenue,orders_count'
        ]);

        $limit = $request->get('limit', 10);
        $period = $request->get('period', 'month');
        $itemTypeId = $request->get('item_type_id');
        $orderBy = $request->get('order_by', 'quantity');

        // Calculer la date de début selon la période
        $dateFrom = $this->getDateFromPeriod($period);

        // Construction de la requête principale
        $query = DB::table('order_item')
            ->join('item', 'order_item.item_id', '=', 'item.item_id')
            ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
            ->select(
                'item.item_id',
                'item.name as item_name',
                'item.description',
                'item.image_link',
                'item.price as current_price',
                'item.quantity as stock_quantity',
                'item.item_type_id',
                DB::raw('SUM(order_item.item_quantity) as total_quantity_ordered'),
                DB::raw('COUNT(DISTINCT order_item.order_id) as number_of_orders'),
                DB::raw('SUM(order_item.item_quantity * item.price) as total_revenue'),
                DB::raw('AVG(order_item.item_quantity) as avg_quantity_per_order'),
                DB::raw('MIN(orders.order_date) as first_order_date'),
                DB::raw('MAX(orders.order_date) as last_order_date')
            )
            ->groupBy(
                'item.item_id',
                'item.name',
                'item.description',
                'item.image_link',
                'item.price',
                'item.quantity',
                'item.item_type_id'
            );

        // Filtre par période
        if ($period !== 'all' && $dateFrom) {
            $query->where('orders.order_date', '>=', $dateFrom);
        }

        // Filtre par type d'item
        if ($itemTypeId) {
            $query->where('item.item_type_id', $itemTypeId);
        }

        // Tri selon le critère choisi
        switch ($orderBy) {
            case 'revenue':
                $query->orderByDesc(DB::raw('SUM(order_item.item_quantity * item.price)'));
                break;
            case 'orders_count':
                $query->orderByDesc(DB::raw('COUNT(DISTINCT order_item.order_id)'));
                break;
            case 'quantity':
            default:
                $query->orderByDesc(DB::raw('SUM(order_item.item_quantity)'));
                break;
        }

        $query->limit($limit);

        $topitems = $query->get();

        // Calculer les statistiques globales
        $globalStats = $this->calculateGlobalStats($period, $dateFrom, $itemTypeId);

        // Enrichir les données avec des calculs supplémentaires
        $enricheditems = $topitems->map(function ($item, $index) use ($globalStats) {
            return [
                'rank' => $index + 1,
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'description' => $item->description,
                'image_link' => $item->image_link,
                'item_type_id' => $item->item_type_id,
                'current_price' => (float) $item->current_price,
                'stock_quantity' => (int) $item->stock_quantity,
                'statistics' => [
                    'total_quantity_ordered' => (int) $item->total_quantity_ordered,
                    'number_of_orders' => (int) $item->number_of_orders,
                    'total_revenue' => (float) $item->total_revenue,
                    'average_quantity_per_order' => round($item->avg_quantity_per_order, 2),
                    'percentage_of_total_quantity' => $globalStats['total_items_sold'] > 0 
                        ? round(($item->total_quantity_ordered / $globalStats['total_items_sold']) * 100, 2) 
                        : 0,
                    'percentage_of_total_revenue' => $globalStats['total_revenue'] > 0 
                        ? round(($item->total_revenue / $globalStats['total_revenue']) * 100, 2) 
                        : 0,
                    'first_order_date' => $item->first_order_date,
                    'last_order_date' => $item->last_order_date
                ]
            ];
        });

        // Préparer les données pour les graphiques
        $chartData = $this->prepareChartData($enricheditems, $orderBy);

        return response()->json([
            'success' => true,
            'filters' => [
                'period' => $period,
                'date_from' => $dateFrom,
                'date_to' => now()->format('Y-m-d'),
                'limit' => $limit,
                'item_type_id' => $itemTypeId,
                'order_by' => $orderBy
            ],
            'global_statistics' => $globalStats,
            'top_items' => $enricheditems,
            'chart_data' => $chartData,
            'generated_at' => now()->toIso8601String()
        ], 200);
    }

    /**
     * Calcule les statistiques globales pour la période donnée
     */
    private function calculateGlobalStats(?string $period, ?string $dateFrom, ?string $itemTypeId): array
    {
        // Total des commandes
        $ordersQuery = Orders::query();
        if ($period !== 'all' && $dateFrom) {
            $ordersQuery->where('order_date', '>=', $dateFrom);
        }
        $totalOrders = $ordersQuery->count();

        // Total des items vendus et revenu
        $itemsQuery = DB::table('order_item')
            ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
            ->join('item', 'order_item.item_id', '=', 'item.item_id');

        if ($period !== 'all' && $dateFrom) {
            $itemsQuery->where('orders.order_date', '>=', $dateFrom);
        }

        if ($itemTypeId) {
            $itemsQuery->where('item.item_type_id', $itemTypeId);
        }

        $stats = $itemsQuery->select(
            DB::raw('SUM(order_item.item_quantity) as total_quantity'),
            DB::raw('SUM(order_item.item_quantity * item.price) as total_revenue'),
            DB::raw('COUNT(DISTINCT order_item.item_id) as unique_items')
        )->first();

        return [
            'total_orders' => $totalOrders,
            'total_items_sold' => (int) ($stats->total_quantity ?? 0),
            'total_revenue' => (float) ($stats->total_revenue ?? 0),
            'unique_items_ordered' => (int) ($stats->unique_items ?? 0),
            'average_items_per_order' => $totalOrders > 0 
                ? round(($stats->total_quantity ?? 0) / $totalOrders, 2) 
                : 0,
            'average_revenue_per_order' => $totalOrders > 0 
                ? round(($stats->total_revenue ?? 0) / $totalOrders, 2) 
                : 0
        ];
    }

    /**
     * Prépare les données pour les graphiques (Chart.js format)
     */
    private function prepareChartData($items, string $orderBy): array
    {
        $colors = $this->generateColors($items->count());

        $chartData = [
            'labels' => [],
            'datasets' => []
        ];

        foreach ($items as $index => $item) {
            $chartData['labels'][] = $item['item_name'];
        }

        // Dataset pour les quantités
        $chartData['datasets'][] = [
            'label' => 'Quantité commandée',
            'data' => $items->pluck('statistics.total_quantity_ordered')->toArray(),
            'backgroundColor' => array_column($colors, 'bg'),
            'borderColor' => array_column($colors, 'border'),
            'borderWidth' => 2
        ];

        // Dataset pour le revenu
        $chartData['datasets'][] = [
            'label' => 'Revenu généré (FCFA)',
            'data' => $items->pluck('statistics.total_revenue')->toArray(),
            'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 2,
            'yAxisID' => 'y-revenue'
        ];

        // Dataset pour le nombre de commandes
        $chartData['datasets'][] = [
            'label' => 'Nombre de commandes',
            'data' => $items->pluck('statistics.number_of_orders')->toArray(),
            'backgroundColor' => 'rgba(153, 102, 255, 0.6)',
            'borderColor' => 'rgba(153, 102, 255, 1)',
            'borderWidth' => 2
        ];

        return $chartData;
    }

    /**
     * Calcule la date de début selon la période
     */
    private function getDateFromPeriod(string $period): ?string
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
     * Génère un tableau de couleurs pour les graphiques
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
            ['bg' => 'rgba(199, 199, 199, 0.6)', 'border' => 'rgba(199, 199, 199, 1)'],
            ['bg' => 'rgba(83, 102, 255, 0.6)', 'border' => 'rgba(83, 102, 255, 1)'],
            ['bg' => 'rgba(255, 83, 112, 0.6)', 'border' => 'rgba(255, 83, 112, 1)'],
            ['bg' => 'rgba(40, 167, 69, 0.6)', 'border' => 'rgba(40, 167, 69, 1)'],
        ];

        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }

    /**
     * Récupère les détails d'un item spécifique avec ses statistiques de commande
     */
    public function getitemOrderStats(Request $request, string $itemId)
    {
        $period = $request->get('period', 'month');
        $dateFrom = $this->getDateFromPeriod($period);

        $item = item::with('type')->findOrFail($itemId);

        // Statistiques de commande pour cet item
        $statsQuery = DB::table('order_item')
            ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
            ->where('order_item.item_id', $itemId);

        if ($period !== 'all' && $dateFrom) {
            $statsQuery->where('orders.order_date', '>=', $dateFrom);
        }

        $stats = $statsQuery->select(
            DB::raw('SUM(order_item.item_quantity) as total_quantity'),
            DB::raw('COUNT(DISTINCT order_item.order_id) as total_orders'),
            DB::raw('AVG(order_item.item_quantity) as avg_per_order'),
            DB::raw('MIN(orders.order_date) as first_order'),
            DB::raw('MAX(orders.order_date) as last_order')
        )->first();

        // Évolution des commandes par jour
        $dailyStats = DB::table('order_item')
            ->join('orders', 'order_item.order_id', '=', 'orders.order_id')
            ->where('order_item.item_id', $itemId)
            ->when($period !== 'all' && $dateFrom, function($q) use ($dateFrom) {
                return $q->where('orders.order_date', '>=', $dateFrom);
            })
            ->select(
                DB::raw('DATE(orders.order_date) as date'),
                DB::raw('SUM(order_item.item_quantity) as quantity'),
                DB::raw('COUNT(DISTINCT order_item.order_id) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'item' => $item,
            'period' => $period,
            'statistics' => [
                'total_quantity_ordered' => (int) ($stats->total_quantity ?? 0),
                'total_orders' => (int) ($stats->total_orders ?? 0),
                'average_per_order' => round($stats->avg_per_order ?? 0, 2),
                'first_order_date' => $stats->first_order ?? null,
                'last_order_date' => $stats->last_order ?? null,
                'total_revenue' => ($stats->total_quantity ?? 0) * $item->price
            ],
            'daily_evolution' => $dailyStats,
            'generated_at' => now()->toIso8601String()
        ], 200);
    }
}