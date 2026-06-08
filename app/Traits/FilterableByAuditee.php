<?php

namespace App\Traits;

use App\Helpers\AuthHelper;

trait FilterableByAuditee
{
    /**
     * Scope a query to only include records related to the current auditee's area/auditee.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $relation
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCurrentAuditee($query, string $relation = 'perencanaanAudit')
    {
        if (!AuthHelper::isAuditee()) {
            return $query;
        }

        $areaId = AuthHelper::getUserAreaId();

        if ($relation === 'self') {
            if ($areaId !== null) {
                $query->where('area_id', $areaId);
            }
            return $query;
        }

        return $query->whereHas($relation, function ($q) use ($areaId) {
            if ($areaId !== null) {
                $q->where('area_id', $areaId);
            }
        });
    }
}
