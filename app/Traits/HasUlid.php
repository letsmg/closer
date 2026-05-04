<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasUlid
 * 
 * Ofusca IDs incrementais usando ULIDs (Universally Unique Lexicographically Sortable Identifiers).
 * ULIDs são melhores que UUIDs para URLs pois são sortable e mais curtos.
 * 
 * Uso: Adicione `use HasUlid;` no model e crie a migration com `$table->ulid('uuid')->unique();`
 */
trait HasUlid
{
    /**
     * Boot do trait - gera ULID automaticamente na criação
     */
    protected static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getUlidColumn()})) {
                $model->{$model->getUlidColumn()} = (string) Str::ulid();
            }
        });
    }

    /**
     * Nome da coluna que armazena o ULID público
     */
    public function getUlidColumn(): string
    {
        return 'uuid';
    }

    /**
     * Route key name - usado para URLs e route model binding
     * Isso faz com que o Laravel use o UUID ao invés do ID nas rotas
     */
    public function getRouteKeyName(): string
    {
        return $this->getUlidColumn();
    }

    /**
     * Get the route key (UUID) para URLs
     */
    public function getRouteKey(): string
    {
        return $this->{$this->getUlidColumn()};
    }

    /**
     * Resolve o model pelo ULID ao invés do ID
     */
    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->where($this->getUlidColumn(), $value)->first();
    }

    /**
     * Resolve relacionamentos pelo ULID
     */
    public function resolveChildRouteBinding($childType, $value, $field): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->where($this->getUlidColumn(), $value)->first();
    }

    /**
     * Scope para buscar por ULID
     */
    public function scopeByUlid($query, string $ulid)
    {
        return $query->where($this->getUlidColumn(), $ulid);
    }

    /**
     * Retorna o ID real apenas para uso interno (nunca exponha na API!)
     * Marca como deprecated para evitar uso acidental
     * 
     * @deprecated Use apenas para relacionamentos internos
     */
    public function getInternalId(): int
    {
        return $this->getAttribute($this->primaryKey);
    }
}
