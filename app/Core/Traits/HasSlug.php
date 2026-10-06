<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Gives a model a stable, public, URL-safe identifier.
 *
 * The slugs used to be built from the title through a Bangla-to-English
 * glossary ("জাতীয় বিজ্ঞান মেলা" became "national-science-fair"). That
 * translation was dropped: for a school's records, a plain serial that an
 * administrator reads aloud — "notice-12", "campus-event-3" — is clearer than
 * an English guess, and nothing a title could name is lost in the address.
 *
 * A manually entered slug is respected; everything else gets the model prefix
 * with the primary key as the serial. The slug is set once, on create, and is
 * deliberately left alone afterwards: a published link that stops working
 * because someone fixed a typo is worse than a slug that is slightly out of
 * date.
 *
 * @property string|null $slug
 *
 * @method static void created(callable $callback)
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        // The primary key only exists once the row has been inserted, so the
        // slug cannot be built until here. The column is nullable for that
        // reason, and this fills it in immediately after the insert rather than
        // leaving the row unroutable.
        static::created(function (Model $model): void {
            if (blank($model->slug)) {
                $model->forceFill(['slug' => $model->slugFallback()])->saveQuietly();
            }
        });
    }

    /**
     * The serial identifier, unique per table because the primary key keeps it
     * apart from every other row that shares the same prefix.
     */
    protected function slugFallback(): string
    {
        return $this->slugPrefix().'-'.$this->getKey();
    }

    /**
     * The word a serial slug is built on, which also keeps the serials in one
     * table from being mistaken for those in another.
     */
    abstract protected function slugPrefix(): string;
}
