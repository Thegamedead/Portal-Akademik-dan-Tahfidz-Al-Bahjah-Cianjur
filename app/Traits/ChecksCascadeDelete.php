<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait ChecksCascadeDelete
{
    /**
     * Check if model has related records and throw exception with detailed message
     *
     * @param Model $model The model to check
     * @param array $relationships Array of [relationship_name => display_name]
     * @param string $entityName Display name of the entity being deleted
     * @throws \Exception
     */
    protected function checkDependencies(Model $model, array $relationships, string $entityName): void
    {
        $dependencies = [];

        foreach ($relationships as $relationshipName => $displayName) {
            if (method_exists($model, $relationshipName)) {
                $count = $model->$relationshipName()->count();
                if ($count > 0) {
                    $dependencies[] = "{$count} {$displayName}";
                }
            }
        }

        if (!empty($dependencies)) {
            $dependencyList = implode(', ', $dependencies);
            throw new \Exception(
                "Tidak dapat menghapus {$entityName} ini karena masih memiliki data terkait: {$dependencyList}. " .
                "Hapus atau pindahkan data terkait terlebih dahulu."
            );
        }
    }

    /**
     * Check specific relationship and throw exception if has records
     *
     * @param Model $model
     * @param string $relationshipName
     * @param string $entityName
     * @param string $dependencyName
     * @throws \Exception
     */
    protected function checkSingleDependency(Model $model, string $relationshipName, string $entityName, string $dependencyName): void
    {
        if (method_exists($model, $relationshipName)) {
            $count = $model->$relationshipName()->count();
            if ($count > 0) {
                throw new \Exception(
                    "Tidak dapat menghapus {$entityName} ini karena masih memiliki {$count} {$dependencyName}. " .
                    "Hapus {$dependencyName} terlebih dahulu."
                );
            }
        }
    }

    /**
     * Check if model is referenced by foreign keys in other tables
     *
     * @param string $tableName
     * @param int $id
     * @param array $foreignKeys Array of [table_name => [column_name, display_name]]
     * @param string $entityName
     * @throws \Exception
     */
    protected function checkForeignKeyReferences(string $tableName, int $id, array $foreignKeys, string $entityName): void
    {
        $references = [];

        foreach ($foreignKeys as $table => $config) {
            $column = $config[0];
            $displayName = $config[1];

            $count = \DB::table($table)->where($column, $id)->count();
            if ($count > 0) {
                $references[] = "{$count} {$displayName} di tabel {$table}";
            }
        }

        if (!empty($references)) {
            $referenceList = implode(', ', $references);
            throw new \Exception(
                "Tidak dapat menghapus {$entityName} ini karena masih dirujuk oleh: {$referenceList}. " .
                "Hapus atau update referensi tersebut terlebih dahulu."
            );
        }
    }

    /**
     * Get dependency summary for a model
     *
     * @param Model $model
     * @param array $relationships
     * @return array [relationship_name => count]
     */
    protected function getDependencySummary(Model $model, array $relationships): array
    {
        $summary = [];

        foreach ($relationships as $relationshipName => $displayName) {
            if (method_exists($model, $relationshipName)) {
                $count = $model->$relationshipName()->count();
                if ($count > 0) {
                    $summary[$displayName] = $count;
                }
            }
        }

        return $summary;
    }

    /**
     * Check if deletion is safe (no dependencies)
     *
     * @param Model $model
     * @param array $relationships
     * @return bool
     */
    protected function canSafelyDelete(Model $model, array $relationships): bool
    {
        foreach ($relationships as $relationshipName => $displayName) {
            if (method_exists($model, $relationshipName)) {
                if ($model->$relationshipName()->count() > 0) {
                    return false;
                }
            }
        }

        return true;
    }
}
