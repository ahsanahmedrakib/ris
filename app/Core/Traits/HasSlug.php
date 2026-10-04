<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Gives a model a stable, public, URL-safe identifier.
 *
 * The slug is derived from the title once, on create, and is deliberately left
 * alone afterwards: a published link that stops working because someone fixed a
 * typo is worse than a slug that is slightly out of date. An admin can still
 * set one explicitly, and that is the only thing that changes it later.
 *
 * Titles here are written in Bengali, and Str::slug() drops every character it
 * cannot transliterate, which for those titles means the empty string. So when
 * the title yields nothing usable the slug falls back to the prefix and the
 * primary key, and the edit form lets a human put something readable there.
 *
 * @property string|null $slug
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            if (blank($model->slug)) {
                $model->slug = $model->slugFromTitle();
            }
        });

        // The primary key only exists once the row has been inserted, so a title
        // with nothing transliteratable in it cannot be slugged until here. The
        // column is nullable for that reason, and this fills it in immediately
        // after the insert rather than leaving the row unroutable.
        static::created(function (Model $model): void {
            if (blank($model->slug)) {
                $model->forceFill(['slug' => $model->slugFallback()])->saveQuietly();
            }
        });
    }

    /**
     * The slug built from the title, or null when the title has no Latin
     * characters in it to build one from.
     */
    protected function slugFromTitle(): ?string
    {
        $slug = Str::slug((string) $this->title);

        if ($slug === '') {
            return null;
        }

        return $this->uniqueSlug($slug);
    }

    /**
     * The identifier used when there is no title to slug, unique per table
     * because it is prefixed by the model.
     */
    protected function slugFallback(): string
    {
        return $this->slugPrefix().'-'.$this->getKey();
    }

    /**
     * The first free "<slug>", "<slug>-2", "<slug>-3" ... Soft deleted rows are
     * included because their slug is still held by the unique index, so ignoring
     * them here would hand out a value the database then rejects.
     */
    protected function uniqueSlug(string $slug): string
    {
        $candidate = $slug;
        $suffix = 1;

        while ($this->newQuery()->where('slug', $candidate)->exists()) {
            $suffix++;
            $candidate = $slug.'-'.$suffix;
        }

        return $candidate;
    }

    /**
     * The word a fallback slug is built on, which also keeps the fallback from
     * colliding with a real title slug in the same table.
     */
    abstract protected function slugPrefix(): string;
}
