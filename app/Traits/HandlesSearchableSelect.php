<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

trait HandlesSearchableSelect
{
    /**
     * Ejecuta una búsqueda paginada/seleccionable con cache seguro.
     *
     * @param string $cachePrefix Prefijo único por endpoint (ej: 'products')
     * @param Builder $query Query base ya con ->select(...) aplicado
     * @param array $validated Datos ya validados del FormRequest (search, selected)
     * @param callable $searchCallback function(Builder $q, string $search): void
     * @param int $limit
     * @param int $ttlSeconds
     */
    protected function searchableSelect(
        string $cachePrefix,
        Builder $query,
        array $validated,
        callable $searchCallback,
        int $limit = 10,
        int $ttlSeconds = 300
    ) {
        $search = $validated['search'] ?? '';
        $selected = $validated['selected'] ?? [];

        $cacheKey = $this->buildSearchCacheKey($cachePrefix, $search, $selected, $limit);

        return Cache::remember($cacheKey, $ttlSeconds, function () use ($query, $search, $selected, $searchCallback, $limit) {
            if (!empty($selected)) {
                $query->whereIn('uuid', $selected);
            } else {
                if ($search !== '') {
                    $searchCallback($query, $search);
                }
                $query->limit($limit);
            }

            return $query->get();
        });
    }

    /**
     * Cache key explícito y acotado — nunca desde $request->all().
     */
    protected function buildSearchCacheKey(string $prefix, string $search, array $selected, int $limit): string
    {
        sort($selected); // para que el orden no genere keys distintas
        $normalizedSearch = mb_strtolower(trim($search));

        return sprintf(
            '%s_%s_%s_%d',
            $prefix,
            md5($normalizedSearch),
            md5(implode(',', $selected)),
            $limit
        );
    }
}
