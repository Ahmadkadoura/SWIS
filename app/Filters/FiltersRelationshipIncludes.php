<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersRelationshipIncludes implements Filter
{
    protected $relationshipIncludes;
    protected $fieldToFilterOn;
    protected $enumClass;

    /**
     * @param array $relationshipIncludes - array of relationships to traverse
     * @param string $fieldToFilterOn - the actual field where the value will be filtered
     * @param string|null $enumClass - the enum class for dynamic transformation (optional)
     */
    public function __construct(array $relationshipIncludes, ?string $fieldToFilterOn = null, ?string $enumClass = null)
    {
        $this->relationshipIncludes = $relationshipIncludes;
        $this->fieldToFilterOn = $fieldToFilterOn;
        $this->enumClass = $enumClass;
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @param string $property
     *
     * $property is the filter name passed in the URL query (e.g., 'gender')
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        // Traverse through the relationships
        $relationship = implode('.', $this->relationshipIncludes);

        if (is_null($this->fieldToFilterOn)) {
            // If no field is specified, just check the existence of the relationship
            $query->whereHas($relationship);
        } else {
            // Apply the filter to the specified field in the related model
            $query->whereHas($relationship, function (Builder $query) use ($value) {
                if (is_array($value)) {
                    // Use whereIn for arrays to support multiple filter values
                    $query->whereIn($this->fieldToFilterOn, $value);
                } else {
                    // Apply a simple where clause for a single value
                    $query->where($this->fieldToFilterOn, $value);
                }
            });
        }
    }
}
