<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait HandlesSearchableSelect
{
    /**
     * Ejecuta una búsqueda paginada/seleccionable con cache seguro.
     *
     * @param string   $cachePrefix    Prefijo único por endpoint (ej: 'products')
     * @param Builder $builder Query base ya con ->select(...) aplicado
     * @param array    $validated      Datos ya validados del FormRequest (search, selected)
     * @param callable $searchCallback function(Builder $q, string $search): void
     */
    protected function searchableSelect(
        string $cachePrefix,
        Builder $builder,
        array $validated,
        callable $searchCallback,
        int $limit = 10,
        int $ttlSeconds = 300
    ) {
        $search = $validated['search'] ?? '';
        $selected = $validated['selected'] ?? [];
        $cacheKey = $this->buildSearchCacheKey($cachePrefix, $search, $selected, $limit);

        return Cache::remember($cacheKey, $ttlSeconds, function () use ($builder, $search, $selected, $searchCallback, $limit) {
            if (!empty($selected)) {
                $builder->whereIn('uuid', $selected);
            } else {
                if ($search !== '') {
                    $searchCallback($builder, $search);
                }

                $builder->limit($limit);
            }

            return $builder->get();
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
